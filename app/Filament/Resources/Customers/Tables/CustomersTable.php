<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])

            ->recordActions([
                EditAction::make(),

                Action::make('send_wa')
                    ->label('Kirim Struk')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn($record) => $record->sales()->exists())
                    ->url(function ($record) {

                        $lastSale = $record->sales()
                            ->latest()
                            ->with('items.product')
                            ->first();

                        if (! $lastSale) return null;

                        // Format WA number
                        $wa = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($wa, '0')) {
                            $wa = '62' . substr($wa, 1);
                        }

                        // Build WhatsApp message
                        $msg  = "Halo {$record->name},%0A";
                        $msg .= "Berikut struk transaksi terbaru Anda:%0A%0A";
                        $msg .= "Invoice: {$lastSale->invoice_number}%0A";
                        $msg .= "Tanggal: " . $lastSale->created_at->format('d-m-Y H:i:s') . "%0A%0A";

                        foreach ($lastSale->items as $item) {
                            $msg .= "{$item->product->name} ({$item->quantity}x) = Rp "
                                . number_format($item->line_total, 0, ',', '.') . "%0A";
                        }

                        if (!empty($lastSale->notes)) {
                            $msg .= "%0ACatatan: {$lastSale->notes}%0A";
                        }

                        $msg .= "%0ATotal: Rp " . number_format($lastSale->total, 0, ',', '.') . "%0A";
                        $msg .= "%0ATerima kasih telah berbelanja! 😊";

                        return "https://wa.me/{$wa}?text={$msg}";
                    })
                    ->openUrlInNewTab(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
