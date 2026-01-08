<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class SalesTrendChart extends ChartWidget
{
    protected ?string $heading = 'Sales Trend (Last 30 Days)';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $from = Carbon::now()->subDays(29)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $rows = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $values = [];

        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->subDays(29 - $i)->toDateString();

            $labels[] = Carbon::parse($date)->format('M d');
            $values[] = isset($rows[$date])
                ? (float) $rows[$date]->total
                : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $values,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }
}
