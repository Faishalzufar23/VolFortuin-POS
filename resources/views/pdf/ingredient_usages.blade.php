<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemakaian Bahan Baku</title>

    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 5px; }
        .range { margin-top: 0; font-size: 12px; }

        .section-title {
            margin-top: 40px;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <h2>Laporan Pemakaian Bahan Baku</h2>
    <p class="range">Periode: {{ $from }} s/d {{ $until }}</p>

    {{-- ========================= TABEL UTAMA ========================= --}}
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Bahan Baku</th>
                <th>Jumlah Digunakan</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $r)
                <tr>
                    <td>{{ $r->created_at->format('d M Y - H:i') }}</td>
                    <td>{{ $r->product->name }}</td>
                    <td>{{ $r->ingredient->name }}</td>
                    <td>{{ number_format($r->quantity_used, 0, ',', '.') }}</td>
                    <td>{{ $r->ingredient->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    {{-- =======================================================================================
        RINGKASAN PER PRODUK (DALAM TABEL)
    ======================================================================================== --}}
    @php
        $groupByProduct = $records->groupBy('product_id');

        // total produk keluar = sum quantity di sale_items
        $productSales = [];
        foreach ($records as $r) {
            $sales = $r->product->saleItems()->sum('quantity');
            $productSales[$r->product_id] = $sales;
        }
    @endphp

    <div class="section-title">Ringkasan Pemakaian per Produk</div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Bahan Baku</th>
                <th>Total Digunakan</th>
                <th>Satuan</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($groupByProduct as $productId => $rows)
            @php
                $productName = $rows->first()->product->name;
                $totalOutput = $productSales[$productId] ?? 0;
                $byIngredient = $rows->groupBy('ingredient_id');
                $rowspan = count($byIngredient);
                $firstRow = true;
            @endphp

            @foreach ($byIngredient as $ingId => $list)
                @php
                    $ingredient = $list->first()->ingredient;
                    $totalQty = $list->sum('quantity_used');
                @endphp

                <tr>
                    @if ($firstRow)
                        <td rowspan="{{ $rowspan }}">
                            {{ $productName }} ({{ $totalOutput }}x)
                        </td>
                        @php $firstRow = false; @endphp
                    @endif

                    <td>{{ $ingredient->name }}</td>
                    <td>{{ number_format($totalQty, 0, ',', '.') }}</td>
                    <td>{{ $ingredient->unit }}</td>
                </tr>
            @endforeach

        @endforeach
        </tbody>
    </table>


    {{-- =======================================================================================
        RINGKASAN PER BAHAN (DALAM TABEL)
    ======================================================================================== --}}
    @php
        $groupByIngredient = $records->groupBy('ingredient_id');
    @endphp

    <div class="section-title">Ringkasan Total Pemakaian per Bahan Baku</div>

    <table>
        <thead>
            <tr>
                <th>Bahan Baku</th>
                <th>Total Digunakan</th>
                <th>Satuan</th>
                <th>Total HPP</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($groupByIngredient as $ingId => $list)
            @php
                $ingredient = $list->first()->ingredient;
                $totalQty = $list->sum('quantity_used');
                $totalHpp = $list->sum(fn($i) => $i->quantity_used * ($i->ingredient->price ?? 0));
            @endphp

            <tr>
                <td>{{ $ingredient->name }}</td>
                <td>{{ number_format($totalQty, 0, ',', '.') }}</td>
                <td>{{ $ingredient->unit }}</td>
                <td>Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


    {{-- =======================================================================================
        GRAND TOTAL
    ======================================================================================== --}}
    @php
        $grandTotal = $records->sum(function($r){
            return ($r->ingredient->price ?? 0) * $r->quantity_used;
        });
    @endphp

    <div class="section-title">Grand Total Biaya Pemakaian Bahan</div>

    <table>
        <thead>
            <tr>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
