<?php

namespace App\Filament\Resources\IngredientUsages\Pages;

use App\Filament\Resources\IngredientUsages\IngredientUsageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Barryvdh\DomPDF\Facade\Pdf;

class ListIngredientUsages extends ListRecords
{
    protected static string $resource = IngredientUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol create bawaan
            CreateAction::make(),

            // Tombol download PDF
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    DatePicker::make('from')->label('From')->required(),
                    DatePicker::make('until')->label('Until')->required(),
                ])
                ->action(function (array $data) {

                    $records = \App\Models\IngredientUsage::query()
                        ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']))
                        ->get();

                    $pdf = Pdf::loadView('pdf.ingredient_usages', [
                        'records' => $records,
                        'from' => $data['from'],
                        'until' => $data['until'],
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "laporan-pemakaian-{$data['from']}-sd-{$data['until']}.pdf"
                    );
                }),
        ];
    }
}
