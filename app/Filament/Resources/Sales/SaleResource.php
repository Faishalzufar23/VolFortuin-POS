<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\Tables\SalesTable;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Models\Sale;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BadgeColumn;




class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable(),

                // TextColumn::make('items_count')
                //     ->label('Item'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->colors([
                        'success' => 'cash',
                        'info' => 'qris',
                    ])
                    ->formatStateUsing(
                        fn($state) =>
                        strtoupper($state)
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                // MENU COLUMN YANG BENAR
                TextColumn::make('menu')
                    ->label('Menu')
                    ->state(
                        fn($record) =>
                        $record->items
                            ->map(fn($i) => "{$i->name} ({$i->quantity})")
                            ->toArray()
                    )
                    ->wrap(),
            ])



            ->filters([
                //
            ])
            ->recordActions([
                // Jika Anda ingin menambah detail action nanti, letakkan di sini
            ])
            ->bulkActions([
                // Jika ingin bulk delete di masa depan
            ]);
    }



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items', 'items.product', 'user']);
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
        ];
    }
}
