<style>
    .detail-modal {
        font-size: 14px;
        padding: 6px 0;
    }
</style>

<div style="padding: 20px; font-size: 15px; line-height: 1.5;">

    <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">
        Detail Pesanan - {{ $order->invoice_number }}
    </h2>

    <div style="margin-bottom: 8px;">
        <strong>Kasir:</strong> {{ $order->user->name }}
    </div>

    <div style="margin-bottom: 16px;">
        <strong>Tanggal:</strong> {{ $order->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }}
    </div>

    {{-- CUSTOMER INFO --}}
    @if ($order->customer)
        <div style="margin-bottom: 6px;">
            <strong>Customer:</strong> {{ $order->customer->name }}
        </div>

        <div style="margin-bottom: 16px;">
            <strong>WhatsApp:</strong> {{ $order->customer->phone }}
        </div>
    @endif

    {{-- ORDER ITEMS --}}
    @foreach ($order->items as $item)
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
            <div>
                {{ $item->product->name }}
                <span style="color:#555;">{{ $item->quantity }} x {{ 'IDR ' . number_format($item->price,0,',','.') }}</span>
            </div>
            <div style="font-weight: bold;">
                {{ 'IDR ' . number_format($item->quantity * $item->price,0,',','.') }}
            </div>
        </div>
    @endforeach

    {{-- NOTES --}}
    @if (!empty($order->notes))
        <div style="margin-top: 12px;">
            <strong>Catatan Tambahan:</strong><br>
            <span style="color:#444;">{{ $order->notes }}</span>
        </div>
    @endif

    <hr style="margin:16px 0;">

    {{-- PAYMENT --}}
    <div><strong>Metode Pembayaran:</strong> {{ strtoupper($order->payment_method) }}</div>

    <div><strong>Dibayar:</strong> {{ 'IDR ' . number_format($order->paid_amount,0,',','.') }}</div>

    @if ($order->payment_method === 'cash')
        <div><strong>Kembalian:</strong> {{ 'IDR ' . number_format($order->change_amount,0,',','.') }}</div>
    @endif

    <div style="margin-top:10px; font-weight:bold; font-size:16px;">
        Total Pembayaran : {{ 'IDR ' . number_format($order->total,0,',','.') }}
    </div>

    {{-- ========================= --}}
    {{-- WHATSAPP SEND BUTTON FIXED --}}
    {{-- ========================= --}}
    @if ($order->customer && $order->customer->phone)

        @php
            // Format nomor WA
            $wa = preg_replace('/[^0-9]/','', $order->customer->phone);
            if (str_starts_with($wa,'0')) {
                $wa = '62' . substr($wa,1);
            }

            // Build message
            $msg  = "Halo {$order->customer->name},%0A";
            $msg .= "Berikut struk belanja Anda:%0A%0A";
            $msg .= "Invoice: {$order->invoice_number}%0A";
            $msg .= "Tanggal: " . $order->created_at->format('d-m-Y H:i:s') . "%0A%0A";

            foreach ($order->items as $i) {
                $msg .= "{$i->product->name} ({$i->quantity}x) - IDR " .
                        number_format($i->line_total,0,',','.') . "%0A";
            }

            if (!empty($order->notes)) {
                $msg .= "%0ACatatan: " . urlencode($order->notes) . "%0A";
            }

            $msg .= "%0ATotal: IDR " . number_format($order->total,0,',','.');
            $msg .= "%0A%0ATerima kasih 😊";
        @endphp

        <div style="margin-top: 20px; text-align:center;">
            <a href="https://wa.me/{{ $wa }}?text={{ $msg }}" target="_blank"
                style="display:inline-block;
                       background:#25D366;
                       padding:10px 20px;
                       border-radius:6px;
                       color:white;
                       font-weight:bold;
                       font-size:15px;
                       text-decoration:none;">
                📩 Kirim Struk via WhatsApp
            </a>
        </div>

    @endif

</div>
