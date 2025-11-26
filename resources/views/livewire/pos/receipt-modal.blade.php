@if ($showReceipt && $receiptData)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-5 w-[360px]">

        {{-- HEADER --}}
        <div class="text-center mb-3">
            <h2 class="text-lg font-bold">MyCoffee & Resto</h2>
            <p class="text-sm text-gray-500">Jl. Contoh No. 123, Surabaya</p>
            <p class="text-sm text-gray-500">Telp: 0812-3456-7890</p>
        </div>

        <hr class="my-2">

        {{-- INFO TRANSAKSI --}}
        <p><strong>Invoice:</strong> {{ $receiptData->invoice_number }}</p>
        <p><strong>Tanggal:</strong> {{ $receiptData->created_at->format('d-m-Y') }}</p>
        <p><strong>Jam:</strong> {{ $receiptData->created_at->format('H:i:s') }}</p>
        <p><strong>Kasir:</strong> {{ $receiptData->user->name }}</p>

        <hr class="my-3">

        {{-- LIST ITEM --}}
        @foreach ($receiptData->items as $item)
            <div class="flex justify-between text-sm mb-1">
                <span>{{ $item->product->name }} ({{ $item->quantity }}x)</span>
                <span>Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <hr class="my-3">

        {{-- TOTAL --}}
        <div class="text-lg font-bold">
            Total: Rp {{ number_format($receiptData->total, 0, ',', '.') }}
        </div>

        {{-- FOOTER / WIFI --}}
        <div class="mt-4 text-center text-xs text-gray-600">
            <p>WiFi: mycoffee-wifi | Pass: 12345678</p>
        </div>

        {{-- BUTTON KIRIM WHATSAPP --}}
        @php
            // ambil nomor dari customer model ATAU dari kolom sale.customer_phone
            $rawPhone = $receiptData->customer->phone
                ?? $receiptData->customer_phone
                ?? null;
        @endphp

        @if ($rawPhone)
            @php
                // normalisasi nomor
                $wa = preg_replace('/[^0-9]/', '', $rawPhone);
                if (str_starts_with($wa, '0')) {
                    $wa = '62' . substr($wa, 1);
                }

                // pesan whatsapp
                $lines = [
                    "Struk Pembayaran",
                    "------------------------------",
                    "Invoice: {$receiptData->invoice_number}",
                    "Tanggal: " . $receiptData->created_at->format('d-m-Y H:i:s'),
                    "Kasir: {$receiptData->user->name}",
                    "",
                ];

                foreach ($receiptData->items as $i) {
                    $lines[] = "{$i->product->name} ({$i->quantity}x) - Rp " . number_format($i->line_total, 0, ',', '.');
                }

                $lines[] = "";
                $lines[] = "Total: Rp " . number_format($receiptData->total, 0, ',', '.');
                $lines[] = "Terima kasih telah berbelanja!";

                $msg = implode("%0A", array_map('rawurlencode', $lines));
            @endphp

            <a href="https://wa.me/{{ $wa }}?text={{ $msg }}"
               target="_blank"
               class="block w-full bg-green-600 text-white font-semibold text-center py-2 rounded shadow mb-3">
               📩 Kirim Struk via WhatsApp
            </a>
        @endif

        {{-- BUTTON TUTUP --}}
        <button wire:click="$set('showReceipt', false)" class="w-full bg-gray-300 rounded py-2">
            Tutup
        </button>

    </div>
</div>
@endif
