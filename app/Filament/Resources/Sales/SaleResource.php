<?php

namespace App\Filament\Resources\Sales;

use BackedEnum;
use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Tables\SalesTable;
use Illuminate\Database\Eloquent\Model;




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

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                // TextColumn::make('paid_amount')
                //     ->label('Dibayar')
                //     ->money('IDR')
                //     ->sortable(),

                // TextColumn::make('change_amount')
                //     ->label('Kembalian')
                //     ->money('IDR')
                //     ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->colors([
                        'success' => 'cash',
                        'info' => 'qris',
                    ])
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                // 🔥 Kolom Menu → menampilkan jumlah item + klik untuk detail
                TextColumn::make('items_count')
                    ->label('Menu')
                    ->state(fn($record) => 'Detail Pesanan')
                    ->url(fn($record) => null) // cegah default link
                    ->action(
                        Action::make('detail')
                            ->label('Detail Pesanan')
                            ->icon('heroicon-o-eye')
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalWidth('lg')
                            ->modalContent(fn($record) => view('modals.sale-detail-modal', [
                                'order' => $record->load('items.product'),
                            ]))
                    )
                    ->extraAttributes([
                        'class' => 'text-primary font-medium cursor-pointer hover:underline'
                    ])


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
            ->with(['items.product', 'user'])
            ->where('is_closed', false);
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
