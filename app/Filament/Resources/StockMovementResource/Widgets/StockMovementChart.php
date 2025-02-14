<?php
namespace App\Filament\Resources\StockMovementResource\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\StockMovement;
use Carbon\Carbon;

class StockMovementChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'stockMovement';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'StockMovement';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Obtener los movimientos de stock por mes
        $stockMovements = StockMovement::selectRaw('MONTH(fecha_movimiento) as month, SUM(cantidad_movimiento) as total_quantity')
            ->whereYear('fecha_movimiento', Carbon::now()->year) // Filtra por el año actual
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Formatear los datos
        $months = [];
        $quantities = [];
        for ($i = 1; $i <= 12; $i++) {
            // Buscar el total de movimientos por mes o 0 si no existe
            $monthData = $stockMovements->firstWhere('month', $i);
            $months[] = Carbon::create()->month($i)->format('M'); // Nombre del mes
            $quantities[] = $monthData ? $monthData->total_quantity : 0; // Si no hay datos, poner 0
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'StockMovement',
                    'data' => $quantities, // Los datos obtenidos de la base de datos
                ],
            ],
            'xaxis' => [
                'categories' => $months, // Los nombres de los meses
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
}
