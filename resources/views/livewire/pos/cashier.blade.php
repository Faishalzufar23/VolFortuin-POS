<div class="grid grid-cols-12 gap-6">

    {{-- PRODUK --}}
    <div class="col-span-8">
        <input type="text" wire:model="search"
            class="w-full mb-4 p-2 border rounded-lg"
            placeholder="Cari produk...">

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $p)
                <div class="p-4 border rounded-lg shadow hover:shadow-md transition">
                    <h3 class="font-semibold">{{ $p['name'] }}</h3>
                    <p>Rp {{ number_format($p['price']) }}</p>
                    <button wire:click="addToCart({{ $p['id'] }})"
                        class="mt-3 bg-blue-600 text-white px-3 py-1 rounded">
                        Tambah
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- KERANJANG --}}
    <div class="col-span-4 bg-white rounded-lg border shadow p-4">
        <h2 class="font-bold text-lg mb-4">Keranjang</h2>

        @foreach($cart as $id => $item)
            <div class="flex justify-between mb-3">
                <div>
                    <strong>{{ $item['name'] }}</strong><br>
                    <small>Rp {{ number_format($item['price']) }}</small>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="decrement({{ $id }})"
                        class="px-2 border rounded">-</button>

                    <span>{{ $item['quantity'] }}</span>

                    <button wire:click="increment({{ $id }})"
                        class="px-2 border rounded">+</button>

                    <button wire:click="removeItem({{ $id }})"
                        class="text-red-500">x</button>
                </div>
            </div>
        @endforeach

        <hr class="my-3">

        <div class="space-y-2">
            <div>Subtotal: <strong>Rp {{ number_format($this->subTotal) }}</strong></div>
            <div>Pajak (%): <input type="number" wire:model="tax" class="border w-20 p-1 rounded"></div>
            <div>Diskon (Rp): <input type="number" wire:model="discount" class="border w-20 p-1 rounded"></div>
            <div class="text-xl mt-3">TOTAL:
                <strong>Rp {{ number_format($this->total) }}</strong>
            </div>
        </div>

        <button wire:click="checkout"
            class="mt-4 w-full bg-green-600 text-white py-2 rounded-lg">
            Checkout
        </button>
    </div>

</div>
