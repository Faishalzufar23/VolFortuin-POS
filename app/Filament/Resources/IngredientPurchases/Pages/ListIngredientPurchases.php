<?php

namespace App\Filament\Resources\IngredientPurchases\Pages;

use App\Filament\Resources\IngredientPurchases\IngredientPurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Barryvdh\DomPDF\Facade\Pdf;


class ListIngredientPurchases extends ListRecords
{
    protected static string $resource = IngredientPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol default CREATE (tambah laporan)
            \Filament\Actions\CreateAction::make(),

            // Tombol DOWNLOAD PDF
            \Filament\Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from')->label('From')->required(),
                    \Filament\Forms\Components\DatePicker::make('until')->label('Until')->required(),
                ])
                ->action(function (array $data) {
                    $query = \App\Models\IngredientPurchase::query()
                        ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));

                    $records = $query->get();

                    $pdf = Pdf::loadView('pdf.ingredient_purchases', [
                        'records' => $records,
                        'from' => $data['from'],
                        'until' => $data['until'],
                    ]);

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        "laporan-masuk-{$data['from']}-sd-{$data['until']}.pdf"
                    );
                }),
        ];
    }
}
