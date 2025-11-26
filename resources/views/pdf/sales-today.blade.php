<h2>Laporan Penjualan Harian</h2>
<p>Tanggal: {{ $date }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Kasir</th>
            <th>Total</th>
            <th>Metode</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->invoice_number }}</td>
            <td>{{ $sale->user->name ?? '-' }}</td>
            <td>Rp {{ number_format($sale->total) }}</td>
            <td>{{ strtoupper($sale->payment_method) }}</td>
            <td>{{ $sale->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
