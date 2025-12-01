<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .right { text-align: right; }
    </style>
</head>
<body>

<h2>Laporan Keuangan – {{ $tanggal }}</h2>

<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>Harga Jual</th>
            <th>HPP per Porsi</th>
            <th>Jumlah Terjual</th>
            <th>Omzet</th>
            <th>Total HPP</th>
            <th>Laba Kotor</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td class="right">{{ number_format($r['harga_jual']) }}</td>
                <td class="right">{{ number_format($r['hpp_per_porsi']) }}</td>
                <td class="right">{{ $r['qty'] }}</td>
                <td class="right">{{ number_format($r['total_penjualan']) }}</td> {{-- sekarang: OMZET --}}
                <td class="right">{{ number_format($r['total_hpp']) }}</td>
                <td class="right">{{ number_format($r['laba_kotor']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3>Ringkasan</h3>

<table>
    <tr>
        <td>Omzet</td>
        <td class="right">{{ number_format($summary['total_penjualan']) }}</td>
    </tr>

    <tr>
        <td>Total Pajak</td>
        <td class="right">{{ number_format($summary['total_tax']) }}</td>
    </tr>

    <tr>
        <td>Total HPP</td>
        <td class="right">{{ number_format($summary['total_hpp']) }}</td>
    </tr>

    <tr>
        <td><strong>Laba Kotor</strong></td>
        <td class="right">
            <strong>{{ number_format($summary['laba_kotor']) }}</strong>
        </td>
    </tr>
</table>

</body>
</html>
