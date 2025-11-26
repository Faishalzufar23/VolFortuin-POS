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
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $r)
                <tr>
                    <td>{{ $r->ingredient->name }}</td>
                    <td>{{ $r->quantity }}</td>
                    <td>{{ $r->ingredient->unit }}</td>
                    <td>{{ $r->created_at->format('d M Y - H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
