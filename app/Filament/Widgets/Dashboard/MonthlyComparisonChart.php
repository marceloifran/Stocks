<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\asistencia;
use App\Models\comida;
use App\Models\stock;
use Filament\Widgets\ChartWidget;

class MonthlyComparisonChart extends ChartWidget
{
    protected static ?string $heading = 'Comparación Mensual - Últimos 6 meses';
    protected static ?int $sort = 6;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $months = [];
        $attendanceData = [];
        $mealsData = [];
        $stockData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $months[] = $date->format('M Y');

            // Datos de asistencia
            $attendanceData[] = asistencia::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            // Datos de comidas
            $mealsData[] = comida::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

            // Datos de stock
            $stockData[] = stock::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Asistencias',
                    'data' => $attendanceData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ],
                [
                    'label' => 'Comidas',
                    'data' => $mealsData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ],
                [
                    'label' => 'Stock Items',
                    'data' => $stockData,
                    'backgroundColor' => 'rgba(255, 193, 7, 0.8)',
                    'borderColor' => 'rgb(255, 193, 7)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'stepSize' => 10,
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
                'bar' => [
                    'borderWidth' => 2,
                ],
            ],
        ];
    }
}
