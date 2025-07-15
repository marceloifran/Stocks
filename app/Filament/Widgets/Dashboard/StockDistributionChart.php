<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\stock;
use Filament\Widgets\ChartWidget;

class StockDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Stock por Categoría';
    protected static ?int $sort = 5;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Agrupar stock por algún campo (asumiendo que tienes un campo 'categoria' o 'tipo')
        // Si no tienes este campo, puedes agrupar por otro criterio
        $stockByCategory = stock::selectRaw('
            CASE 
                WHEN cantidad > 100 THEN "Alto Stock"
                WHEN cantidad > 50 THEN "Stock Medio"
                WHEN cantidad > 20 THEN "Stock Bajo"
                ELSE "Stock Crítico"
            END as categoria,
            COUNT(*) as total
        ')
            ->groupBy('categoria')
            ->get();

        $labels = $stockByCategory->pluck('categoria')->toArray();
        $data = $stockByCategory->pluck('total')->toArray();

        $colors = [
            'rgba(34, 197, 94, 0.8)',   // Verde - Alto Stock
            'rgba(59, 130, 246, 0.8)',  // Azul - Stock Medio
            'rgba(255, 193, 7, 0.8)',   // Amarillo - Stock Bajo
            'rgba(239, 68, 68, 0.8)',   // Rojo - Stock Crítico
        ];

        $borderColors = [
            'rgb(34, 197, 94)',
            'rgb(59, 130, 246)',
            'rgb(255, 193, 7)',
            'rgb(239, 68, 68)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'white',
                    'bodyColor' => 'white',
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
