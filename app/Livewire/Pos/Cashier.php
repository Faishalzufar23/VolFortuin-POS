<?php

namespace App\Livewire\Pos;

use App\Models\Sale;
use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Ingredient;
use Illuminate\Support\Str;
use App\Models\IngredientUsage;
use App\Models\ProductIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Services\MidtransService;




class Cashier extends Component
{
    public $products = [];
    public $cart = [];
    public $search = '';
    public $tax = 0;
    public $discount = 0;
    public $customer_id = null;
    public $notes = '';
    public $customer_name;
    public $customer_phone;
    public $showReceipt = false;
    public $lastSale;
    public $receiptData = null;
    protected $testing = false;
    public $categoryId = null;
    public $categories = [];





    //testing tidak meload
    public function mount()
    {
        if (!app()->environment('testing')) {
            $this->categories = \App\Models\Category::orderBy('name')->get();
            $this->loadProducts();
        }
    }



    public function loadProducts()
    {
        $query = Product::query()
            ->when($this->categoryId, function ($q) {
                $q->where('category_id', $this->categoryId);
            });

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->products = $query->select(
            'id',
            'name',
            'price',
            'stock',
            'image'
        )
            ->get()
            ->toArray();
    }




    public function updatedSearch()
    {
        $this->loadProducts();
    }

    public function setCategory($categoryId = null)
    {
        $this->categoryId = $categoryId;
        $this->loadProducts();
    }


    public function addToCart($productId)
    {
        $product = Product::with('productIngredients.ingredient')->find($productId);

        if (!$product) return;

        // kalau stok habis
        if ($product->stock <= 0) {
            $this->dispatchBrowserEvent('stok-habis', [
                'message' => "Stok {$product->name} habis!"
            ]);
            return;
        }

        // lanjutkan tambahkan ke cart
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id'       => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        // $this->calculateTotals();
    }




    public function increment($id)
    {
        $this->cart[$id]['quantity']++;
    }

    public function decrement($id)
    {
        if ($this->cart[$id]['quantity'] > 1) {
            $this->cart[$id]['quantity']--;
        }
    }

    public function removeItem($id)
    {
        unset($this->cart[$id]);
    }

    public function getSubTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getTaxAmountProperty()
    {
        return ($this->tax / 100) * $this->subTotal;
    }

    public function getTotalProperty()
    {
        return ($this->subTotal + $this->taxAmount) - $this->discount;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', message: 'Keranjang kosong!');
            return;
        }

        DB::beginTransaction();

        try {

            $invoice = 'INV-' . strtoupper(Str::random(8));

            // 1. Buat / update customer berdasarkan nomor HP
            $customer = null;

            if ($this->customer_name || $this->customer_phone) {
                $customer = Customer::updateOrCreate(
                    ['phone' => $this->customer_phone],
                    ['name' => $this->customer_name ?? 'Guest']
                );
            }

            // 2. Simpan sale + customer_id
            $sale = Sale::create([
                'invoice_number' => $invoice,
                'user_id'        => Auth::id(),
                'customer_id'    => $customer?->id,
                'customer_phone' => $this->customer_phone,
                'notes'          => $this->notes,
                'items_count'    => collect($this->cart)->sum('quantity'),
                'sub_total'      => $this->subTotal,
                'tax'            => $this->taxAmount,
                'discount'       => $this->discount,
                'total'          => $this->total,
                'payment_status' => 'paid',
                'payment_method' => $this->paymentMethod,
                'paid_amount'    => $this->paymentMethod === 'cash'
                    ? $this->cashAmount
                    : $this->total,
                'change_amount'  => $this->paymentMethod === 'cash'
                    ? ($this->cashAmount - $this->total)
                    : 0,
            ]);

            // 3. Items & pengurangan stok
            foreach ($this->cart as $item) {

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'line_total' => $item['quantity'] * $item['price'],
                ]);

                $recipes = ProductIngredient::with('ingredient')
                    ->where('product_id', $item['id'])
                    ->get();

                foreach ($recipes as $recipe) {

                    $ingredient = $recipe->ingredient;

                    if (!$ingredient) {
                        throw new \Exception("Bahan baku tidak ditemukan.");
                    }

                    $used = $recipe->quantity * $item['quantity'];

                    if ($ingredient->stock < $used) {
                        throw new \Exception("Stok {$ingredient->name} kurang.");
                    }

                    $ingredient->decrement('stock', $used);

                    IngredientUsage::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'ingredient_id' => $ingredient->id,
                        'quantity_used' => $used,
                        'unit' => $ingredient->unit,
                    ]);
                }
            }

            DB::commit();

            // Reset
            $this->cart = [];
            $this->notes = null;
            $this->customer_name = null;
            $this->customer_phone = null;

            $this->dispatch('notify', message: 'Transaksi berhasil!');
            $this->loadProducts();
        } catch (\Throwable $e) {
            DB::rollBack();
            dd("Checkout ERROR: " . $e->getMessage());
        }
    }

    public function closeReceipt()
    {
        $this->showReceipt = false;
        $this->lastSale = null;
    }





    public $showPaymentModal = false;
    public $paymentMethod = 'cash';
    public $cashAmount;
    public $showSuccessMessage = false;
    public $stepPayment = 1; // 1 = pilih metode, 2 = input cash, 3 = QRIS popup
    public $changeAmount = 0;
    public $showSuccessModal = false;
    public $successMessage = '';
    public $successChange = 0;


    public function selectPaymentMethod($method)
    {
        $this->paymentMethod = $method;

        if ($method === 'cash') {
            $this->stepPayment = 2;
        } else {
            $this->stepPayment = 3;
        }
    }

    public function calculateChange()
    {
        $this->changeAmount = $this->cashAmount - $this->total;
    }

    public function confirmCashPayment()
    {
        if ($this->cashAmount < $this->total) {
            $this->dispatch('notify', type: 'error', message: 'Uang kurang!');
            return;
        }

        // 🔥 Proses transaksi dulu
        $this->checkout();

        // 🔥 Baru tampilkan popup sukses
        $this->successMessage = 'Pembayaran Cash Berhasil!';
        $this->successChange = $this->cashAmount - $this->total;
        $this->showSuccessModal = true;

        // 🔥 Reset modal payment
        $this->showPaymentModal = false;
        $this->stepPayment = 1;
    }

    public function confirmQrisPayment()
    {
        $this->checkout();

        $this->successMessage = 'Pembayaran QRIS Berhasil!';
        $this->successChange = null;
        $this->showSuccessModal = true;

        $this->showPaymentModal = false;
        $this->stepPayment = 1;
    }


    public $receiptPdfUrl = null;
    public function printReceipt($saleId = null)
    {
        $sale = Sale::with(['items.product', 'user', 'customer'])
            ->latest()
            ->first();

        if (! $sale) {
            $this->dispatch('notify', message: 'Tidak ada transaksi untuk dicetak.');
            return;
        }

        // simpan data transaksi
        $this->receiptData = $sale;

        // 🔥 Ambil nomor WA dari sale -> customer_phone
        $this->receiptData->whatsapp = $sale->customer->phone
            ?? $sale->customer_phone
            ?? null;

        $this->showReceipt = true;
    }

    public function generatePdf(Sale $sale)
    {
        $pdf = Pdf::loadView('receipts.pdf', ['sale' => $sale])
            ->setPaper('a6', 'portrait'); // ukuran kecil format struk

        $fileName = "receipts/{$sale->invoice_number}.pdf";
        Storage::put("public/{$fileName}", $pdf->output());

        return Storage::url($fileName);
    }

    public function isTesting()
    {
        return app()->environment('testing');
    }


    public function render()
    {

        // Cegah Livewire merender blade saat TESTING
        if ($this->isTesting()) {
            return <<<'HTML'
            <div>testing mode</div>
        HTML;
        }

        return view('livewire.pos.cashier');
    }
}
