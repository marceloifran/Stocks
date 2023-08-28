<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MovimientosChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'movimientosChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Historial de Movimientos';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Obtener los datos de movimientos de existencias desde el modelo "StockMovement"
        $stockMovements = StockMovement::orderBy('fecha_movimiento')->pluck('cantidad_movimiento')->toArray();
        $dates = StockMovement::orderBy('fecha_movimiento')->pluck('fecha_movimiento')->toArray();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'MovimientosChart',
                    'data' => $stockMovements,
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
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
            'colors' => ['#33FF5E'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
