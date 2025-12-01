<?php

namespace App\Filament\Resources\FinanceReports\Pages;

use App\Filament\Resources\FinanceReports\FinanceReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ListFinanceReports extends ListRecords
{
    protected static string $resource = FinanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportPdf'),
        ];
    }

    public function exportPdf()
    {
        // Ambil semua product + item + resep
        $products = Product::with(['saleItems', 'productIngredients.ingredient'])->get();

        // Bangun row untuk PDF
        $rows = $products->map(function ($p) {

            $qty = $p->saleItems->sum('quantity');
            $omzet = $p->saleItems->sum('line_total');

            $hpp_per_porsi = $p->hppPerPorsi();
            $total_hpp = $hpp_per_porsi * $qty;

            return [
                'name'            => $p->name,
                'harga_jual'      => $p->price,
                'hpp_per_porsi'   => $hpp_per_porsi,
                'qty'             => $qty,
                'total_penjualan' => $omzet,
                'total_hpp'       => $total_hpp,
                'laba_kotor'      => $omzet - $total_hpp, // nanti dikurangi pajak di summary
            ];
        });

        // 🔥 Ambil TOTAL PAJAK dari tabel sales
        $total_tax = Sale::sum('tax');

        // 🔥 Summary final (lebih akurat)
        $summary = [
            'total_penjualan' => $rows->sum('total_penjualan'), // Omzet
            'total_hpp'       => $rows->sum('total_hpp'),
            'total_tax'       => $total_tax,
            'laba_kotor'      => $rows->sum('total_penjualan')
                                   - $total_tax
                                   - $rows->sum('total_hpp'),
        ];

        // Render PDF
        $pdf = PDF::loadView('pdf.finance-report', [
            'rows'    => $rows,
            'summary' => $summary,
            'tanggal' => now()->format('d M Y'),
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-keuangan-' . now()->format('Ymd_His') . '.pdf'
        );
    }
}
