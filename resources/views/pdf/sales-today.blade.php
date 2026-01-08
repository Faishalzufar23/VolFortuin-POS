<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Hari Ini</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h2 {
            margin-bottom: 5px;
        }

        .meta {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f3f4f6;
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        tbody td {
            border: 1px solid #999;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tfoot td {
            border: 1px solid #999;
            padding: 8px;
            font-weight: bold;
            background: #f9fafb;
        }
    </style>
</head>
<body>

    <h2>Laporan Penjualan Hari Ini</h2>

    <div class="meta">
        <strong>Tanggal:</strong> {{ $date }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kasir</th>
                <th class="text-right">Total</th>
                <th class="text-center">Metode</th>
                <th>Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @php $grandTotal = 0; @endphp

            @forelse ($sales as $sale)
                @php $grandTotal += $sale->total; @endphp
                <tr>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td class="text-right">
                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        {{ strtoupper($sale->payment_method ?? '-') }}
                    </td>
                    <td>
                        {{ $sale->created_at->format('d-m-Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        Tidak ada transaksi hari ini
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td>Grand Total</td>
                <td class="text-right">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
