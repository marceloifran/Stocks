<?php

namespace App\Filament\Resources\StockResource\Widgets;

use App\Models\Stock;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class StockChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'stock';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Análisis de Stocks';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected static ?string $pollingInterval = '10s'; // 10 segundos

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $stocks = Stock::all();
        $stockNames = $stocks->pluck('nombre')->toArray();
        $stockValues = $stocks->map(fn($stock) => $stock->cantidad * $stock->precio)->toArray();
        $totalInventoryValue = array_sum($stockValues);
        $stockQuantities = $stocks->pluck('cantidad')->toArray();
        $averagePrices = $stocks->map(fn($stock) => $stock->precio)->toArray();
        $percentageOfTotal = array_map(fn($value) => ($value / $totalInventoryValue) * 100, $stockValues);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'Valor Total',
                    'data' => $stockValues,
                ],
                [
                    'name' => 'Cantidad Total',
                    'data' => $stockQuantities,
                ],
                [
                    'name' => 'Precio Promedio',
                    'data' => $averagePrices,
                ],
                [
                    'name' => 'Porcentaje del Total',
                    'data' => $percentageOfTotal,
                ],
            ],
            'xaxis' => [
                'categories' => $stockNames,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Valores',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b', '#34d399', '#60a5fa', '#f87171'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => function ($value, $seriesIndex) {
                        if ($seriesIndex === 0) {
                            return '$' . number_format($value, 2);
                        } elseif ($seriesIndex === 1) {
                            return number_format($value) . ' unidades';
                        } elseif ($seriesIndex === 2) {
                            return '$' . number_format($value, 2) . ' por unidad';
                        } else {
                            return number_format($value, 2) . '% del total';
                        }
                    },
                ],
            ],
        ];
    }
}
