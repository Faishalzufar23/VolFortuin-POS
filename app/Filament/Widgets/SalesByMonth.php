<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;

class SalesByMonth extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales';

    protected function getData(): array
    {
        $year = now()->year; // 2026

        $sales = Sale::selectRaw('
            MONTH(created_at) as month_number,
            SUM(total) as total
        ')
            ->whereYear('created_at', $year)
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('total', 'month_number')
            ->toArray();

        $labels = [];
        $data   = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = date('F', mktime(0, 0, 0, $month, 1));
            $data[]   = $sales[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => "Sales {$year}",
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
