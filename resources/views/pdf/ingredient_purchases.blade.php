<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Masuk</title>

    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 0; }
        .range { margin-top: 0; font-size: 12px; }
        .text-right { text-align: right; }
        .summary-title { margin-top: 25px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Laporan Bahan Baku Masuk</h2>
    <p class="range">Periode: {{ $from }} s/d {{ $until }}</p>

    <table>
        <thead>
            <tr>
                <th>Bahan Baku</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga per Unit</th>
                <th>Total HPP</th>
                <th>Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @php
                $grandTotalQty = 0;
                $grandTotalHpp = 0;
            @endphp

            @foreach ($records as $r)
                @php
                    $unitPrice = $r->ingredient->price ?? 0;
                    $totalHpp  = $unitPrice * $r->quantity;

                    $grandTotalQty += $r->quantity;
                    $grandTotalHpp += $totalHpp;
                @endphp

                <tr>
                    <td>{{ $r->ingredient->name }}</td>
                    <td>{{ number_format($r->quantity, 0, ',', '.') }}</td>
                    <td>{{ $r->ingredient->unit }}</td>

                    <td class="text-right">
                        Rp {{ number_format($unitPrice, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($totalHpp, 0, ',', '.') }}
                    </td>

                    <td>{{ $r->created_at->format('d M Y - H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ====================== --}}
    {{--   TABEL RINGKASAN      --}}
    {{-- ====================== --}}

    <h3 class="summary-title">Ringkasan</h3>

    <table>
        <thead>
            <tr>
                <th>Total Item</th>
                <th>Total Kuantitas Masuk</th>
                <th>Total HPP</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ count($records) }}</td>

                <td>
                    {{ number_format($grandTotalQty, 0, ',', '.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($grandTotalHpp, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
