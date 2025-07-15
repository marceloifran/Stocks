<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\asistencia;
use App\Models\comida;
use App\Models\stock;
use Filament\Widgets\ChartWidget;

class ActivityOverviewChart extends ChartWidget
{
    protected static ?string $heading = 'Resumen de Actividad Semanal';
    protected static ?int $sort = 7;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $labels = [];
        $attendanceData = [];
        $mealsData = [];
        $stockData = [];

        // Obtener datos de los últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D d/m');

            // Asistencias del día
            $attendanceData[] = asistencia::whereDate('created_at', $date)->count();

            // Comidas del día
            $mealsData[] = comida::whereDate('created_at', $date)->count();

            // Stock creado del día
            $stockData[] = stock::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Asistencias',
                    'data' => $attendanceData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
                [
                    'label' => 'Comidas',
                    'data' => $mealsData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
                [
                    'label' => 'Stock Items',
                    'data' => $stockData,
                    'backgroundColor' => 'rgba(255, 193, 7, 0.6)',
                    'borderColor' => 'rgb(255, 193, 7)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(255, 193, 7)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $labels,
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
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'white',
                    'bodyColor' => 'white',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'stacked' => false,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'stepSize' => 5,
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
            'elements' => [
                'point' => [
                    'hoverRadius' => 8,
                ],
                'line' => [
                    'borderJoinStyle' => 'round',
                ],
            ],
        ];
    }
}
