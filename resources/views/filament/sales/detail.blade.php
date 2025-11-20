<div class="space-y-3">
    <h3 class="text-lg font-bold">Invoice: {{ $record->invoice_number }}</h3>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">Menu</th>
                <th class="p-2 text-center">Qty</th>
                <th class="p-2 text-right">Harga</th>
                <th class="p-2 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $item)
            <tr>
                <td class="p-2">{{ $item->name }}</td>
                <td class="p-2 text-center">{{ $item->quantity }}</td>
                <td class="p-2 text-right">Rp {{ number_format($item->price) }}</td>
                <td class="p-2 text-right">Rp {{ number_format($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 font-semibold text-right">
        Total: Rp {{ number_format($record->total) }} <br>
        Dibayar: Rp {{ number_format($record->paid_amount) }} <br>
        Kembalian: Rp {{ number_format($record->change_amount) }}
    </div>
</div>
