<?php

namespace App\Filament\Resources\StockResource\Widgets;

use App\Models\stock;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Análisis de Stock - Últimos 30 días';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Datos de stock creado
        $stockData = Trend::model(stock::class)
            ->between(
                start: now()->subDays(29),
                end: now(),
            )
            ->perDay()
            ->count();

        // Datos de cantidad total por día
        $quantityData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $totalQuantity = stock::whereDate('created_at', $date)->sum('cantidad');
            $quantityData[] = $totalQuantity;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Items de Stock Creados',
                    'data' => $stockData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Cantidad Total',
                    'data' => $quantityData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderWidth' => 3,
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(239, 68, 68)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $stockData->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('d/m')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'white',
                    'bodyColor' => 'white',
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Items Creados',
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad Total',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}
