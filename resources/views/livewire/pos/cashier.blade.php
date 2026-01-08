<div class="grid grid-cols-12 gap-6">
    {{-- PRODUK --}}
    <div class="col-span-8">
        <input type="text" wire:model.live="search" class="w-full border rounded-lg p-2 mb-4"
            placeholder="Cari produk...">

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($products as $p)
                <div
                    class="bg-white p-4 border rounded-xl shadow-sm hover:shadow-lg transition text-center flex flex-col">

                    {{-- FOTO PRODUK --}}
                    <div class="w-full h-44 mb-3 rounded-lg overflow-hidden bg-gray-100">
                        @if (!empty($p['image']))
                            <img src="{{ asset('storage/' . $p['image']) }}" class="w-full h-full object-cover"
                                loading="lazy" alt="{{ $p['name'] }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                No Image
                            </div>
                        @endif
                    </div>

                    {{-- NAMA --}}
                    <h3 class="font-semibold text-sm leading-tight">
                        {{ $p['name'] }}
                    </h3>

                    {{-- HARGA --}}
                    <p class="text-green-600 text-sm mt-1">
                        Rp {{ number_format($p['price']) }}
                    </p>

                    @php
                        $disabled = ($p['stock'] ?? 0) <= 0;
                    @endphp

                    {{-- BUTTON --}}
                    <button wire:click="addToCart({{ $p['id'] }})"
                        class="mt-auto w-full px-3 py-2 rounded-lg text-white text-sm font-medium
                        {{ $disabled ? 'bg-red-500 opacity-60 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 active:scale-95' }}"
                        {{ $disabled ? 'disabled' : '' }}>
                        {{ $disabled ? 'Stok Habis' : 'Tambah' }}
                    </button>
                </div>
            @empty
                <p class="col-span-4 text-gray-500 text-center">
                    Produk tidak ditemukan
                </p>
            @endforelse
        </div>
    </div>



    {{-- KERANJANG --}}
    <div class="col-span-4">
        <div class="bg-white rounded-xl border shadow-sm p-5 sticky top-4">

            <h2 class="font-bold text-lg mb-1">Keranjang</h2>
            <p class="text-sm text-gray-500 mb-4">
                {{ empty($cart) ? 'Keranjang masih kosong' : 'Daftar pesanan' }}
            </p>

            {{-- LIST ITEM --}}
            <div class="space-y-4 max-h-[260px] overflow-y-auto pr-1">

                @forelse ($cart as $id => $item)
                    <div wire:key="cart-{{ $id }}"
                        class="flex justify-between items-center border rounded-lg p-3 shadow-sm">

                        <div>
                            <p class="font-semibold text-gray-800 leading-tight">
                                {{ $item['name'] }}
                            </p>
                            <p class="text-gray-500 text-sm">
                                Rp {{ number_format($item['price']) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="decrement({{ $id }})"
                                class="h-7 w-7 flex items-center justify-center border rounded bg-gray-100 hover:bg-gray-200">
                                –
                            </button>

                            <span class="w-6 text-center font-semibold">
                                {{ $item['quantity'] }}
                            </span>

                            <button wire:click="increment({{ $id }})"
                                class="h-7 w-7 flex items-center justify-center border rounded bg-gray-100 hover:bg-gray-200">
                                +
                            </button>

                            <button wire:click="removeItem({{ $id }})"
                                class="text-red-500 font-bold text-lg">
                                ×
                            </button>
                        </div>
                    </div>

                @empty
                    <p class="text-gray-400 text-sm text-center py-6">
                        Belum ada item.
                    </p>
                @endforelse

            </div>

            <hr class="my-4">

            {{-- ADDITIONAL NOTES --}}
            <div class="mb-4">
                <label class="text-sm font-semibold text-gray-700">Catatan Tambahan:</label>
                <textarea wire:model="notes" class="w-full border rounded-lg p-2 text-sm mt-1 focus:ring focus:ring-blue-200"
                    rows="3" placeholder="Contoh: tanpa gula, level es sedikit, catatan khusus pelanggan..."></textarea>
            </div>

            {{-- TOTAL SECTION --}}
            <div class="space-y-3 text-sm">

                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <strong>Rp {{ number_format($this->subTotal) }}</strong>
                </div>

                <div class="flex justify-between items-center">
                    <span>Pajak (%):</span>
                    <input type="number" wire:model="tax" class="border w-24 p-1 rounded text-right">
                </div>

                <div class="flex justify-between items-center">
                    <span>Diskon (Rp):</span>
                    <input type="number" wire:model="discount" class="border w-24 p-1 rounded text-right">
                </div>

                <div class="flex justify-between items-center">
                    <span>Nama Customer:</span>
                    <input type="text" wire:model="customer_name" class="border w-40 p-1 rounded text-right"
                        placeholder="Opsional">
                </div>

                <div class="flex justify-between items-center">
                    <span>No. WhatsApp:</span>
                    <input type="text" wire:model="customer_phone" class="border w-40 p-1 rounded text-right"
                        placeholder="Opsional">
                </div>


                <div class="flex justify-between pt-3 text-xl font-bold">
                    <span>Total:</span>
                    <span>Rp {{ number_format($this->total) }}</span>
                </div>

            </div>

            @if (!empty($cart))
                <button wire:click="$set('showPaymentModal', true)"
                    class="mt-5 bg-green-600 hover:bg-green-700 text-white w-full py-2 rounded-lg font-semibold transition">
                    Checkout
                </button>
            @endif

        </div>
    </div>



    {{-- MODAL PAYMENT --}}
    @if ($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-[520px] shadow-lg">

                {{-- Step 1 --}}
                @if ($stepPayment === 1)
                    <h2 class="text-lg font-bold mb-4 text-center">Pilih Metode Pembayaran</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <button wire:click="selectPaymentMethod('cash')"
                            class="p-4 border rounded-lg text-center bg-gray-100 hover:bg-green-200">
                            💵 Cash
                        </button>

                        <button wire:click="selectPaymentMethod('qris')"
                            class="p-4 border rounded-lg text-center bg-gray-100 hover:bg-blue-200">
                            📱 QRIS
                        </button>
                    </div>
                @endif

                {{-- Step 2 Cash --}}
                @if ($stepPayment === 2)
                    <h2 class="text-lg font-bold mb-4">Pembayaran Cash</h2>

                    <label class="text-sm font-semibold">Masukkan Uang Cash:</label>
                    <input type="number" wire:model="cashAmount" wire:keyup="calculateChange"
                        class="border rounded p-2 w-full mt-1" placeholder="Nominal uang tunai">

                    @if ($cashAmount)
                        <p class="mt-3 text-sm">
                            Total: <strong>Rp {{ number_format($this->total) }}</strong><br>
                            Kembalian:
                            <strong class="text-green-600">Rp {{ number_format($changeAmount) }}</strong>
                        </p>
                    @endif

                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="$set('showPaymentModal', false)"
                            class="px-3 py-2 bg-gray-500 text-white rounded">
                            Batal
                        </button>

                        <button wire:click="confirmCashPayment" class="px-3 py-2 bg-green-600 text-white rounded">
                            Konfirmasi
                        </button>
                    </div>
                @endif

                {{-- Step 3 QRIS --}}
                @if ($stepPayment === 3)
                    <h2 class="text-lg font-bold mb-4 text-center">Scan QRIS</h2>

                    <div class="text-center">
                        <img src="/images/qris-example.png" class="w-52 mx-auto border rounded">
                        <p class="mt-3">Total:
                            <strong>Rp {{ number_format($this->total) }}</strong>
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="$set('showPaymentModal', false)"
                            class="px-3 py-2 bg-gray-500 text-white rounded">
                            Batal
                        </button>

                        <button wire:click="confirmQrisPayment" class="px-3 py-2 bg-blue-600 text-white rounded">
                            Pembayaran Selesai
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif

    {{-- MODAL SUCCESS --}}
    @if ($showSuccessModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">

                <h2 class="text-xl font-bold text-green-600 mb-4">
                    {{ $successMessage }}
                </h2>

                <p class="text-lg font-semibold">
                    Total: Rp {{ number_format($this->total) }}
                </p>

                @if ($successChange !== null)
                    <p class="mt-2 text-md">
                        Kembalian:
                        <span class="font-bold text-blue-600">
                            Rp {{ number_format($successChange) }}
                        </span>
                    </p>
                @endif

                <div class="flex justify-center gap-3 mt-6">
                    <button class="px-4 py-2 bg-gray-700 text-white rounded"
                        wire:click="$set('showSuccessModal', false)">
                        Tutup
                    </button>

                    <button class="px-4 py-2 bg-green-600 text-white rounded" wire:click="printReceipt">
                        Cetak Struk
                    </button>
                </div>

            </div>
        </div>
    @endif

    @include('livewire.pos.receipt-modal')



    <script>
        window.addEventListener('stok-habis', event => {
            alert(event.detail.message);
        });
    </script>

</div>
