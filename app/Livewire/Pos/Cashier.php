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




    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::with('productIngredients.ingredient');

        if (!empty($this->search)) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
        }

        $this->products = $query->get()->toArray();
    }



    public function updatedSearch()
    {
        $this->loadProducts();
    }


    public function addToCart($id)
    {
        $product = Product::findOrFail($id);

        if (!isset($this->cart[$id])) {
            $this->cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => 1,
            ];
        } else {
            $this->cart[$id]['quantity']++;
        }
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
                'customer_id'    => $customer?->id,      // ← FIX PENTING!
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


    public function printReceipt()
    {
        $this->dispatch('notify', message: 'Fitur cetak struk coming soon!');
        $this->showSuccessModal = false;
    }


    public function render()
    {
        return view('livewire.pos.cashier');
    }
}
