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
    protected static ?string $heading = 'Stock';

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
        $stockData = $this->getStockData();
        $months = $this->getMonths();

        return [
            'chart' => [
                'type' => 'line',
                // 'height' => 400,
                // 'width' => 600,
            ],
            'animations' => [
                'enabled' => true,
                'easing' => 'easeinout',
                'speed' => 800,
            ],
            'series' => [
                [
                    'name' => 'Cantidad en Stock',
                    'data' => $stockData,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    /**
     * Obtiene los datos de cantidad por mes.
     *
     * @return array
     */
    private function getStockData(): array
    {
        // Agrupa por mes y suma la cantidad total de stock
        return Stock::selectRaw('SUM(cantidad) as total, MONTH(fecha) as month')
            ->groupByRaw('MONTH(fecha)')
            ->orderByRaw('MONTH(fecha)')
            ->pluck('total')
            ->toArray();
    }

    /**
     * Obtiene los nombres de los meses en orden.
     *
     * @return array
     */
    private function getMonths(): array
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    }
}
