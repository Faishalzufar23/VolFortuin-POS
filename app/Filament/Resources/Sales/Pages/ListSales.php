<?php

namespace App\Filament\Resources\Sales\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Sales\SaleResource;
use Filament\Infolists\Components\RepeatableEntry;


class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_today')
                ->label('Print Penjualan Hari Ini')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action(function () {

                    $sales = \App\Models\Sale::whereDate('created_at', today())
                        ->where('is_closed', false)
                        ->get();

                    if ($sales->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada penjualan hari ini yang bisa dicetak.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                        'pdf.sales-today',
                        ['sales' => $sales, 'date' => now()->format('d M Y')]
                    );

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        'sales-harian-' . now()->format('d-m-Y') . '.pdf'
                    );
                }),


            Actions\Action::make('close_today')
                ->label('Tutup Kasir')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    \App\Models\Sale::whereDate('created_at', today())
                        ->update(['is_closed' => true]);
                }),

        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('detail')
                ->label('Lihat Detail')
                ->icon('heroicon-o-eye')
                ->modalHeading('Detail Pesanan')
                ->modalWidth('lg')
                ->modalContent(function (Model $record) {
                    return view('modals.sale-detail-modal', [
                        'order' => $record->load(['items.product', 'customer']),
                    ]);
                }),
        ];
    }
}
