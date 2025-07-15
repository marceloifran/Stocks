<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\comida;
use Filament\Widgets\ChartWidget;

class MealsChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Comidas por Tipo (Últimos 7 días)';
    protected static ?int $sort = 3;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $labels = [];
        $desayunosData = [];
        $almuerzosData = [];
        $meriendasData = [];
        $cenasData = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dailyMeals = comida::whereDate('created_at', $date)->get();

            $desayunosData[] = $dailyMeals->where('tipo', 'Desayuno')->count();
            $almuerzosData[] = $dailyMeals->where('tipo', 'Almuerzo')->count();
            $meriendasData[] = $dailyMeals->where('tipo', 'Merienda')->count();
            $cenasData[] = $dailyMeals->where('tipo', 'Cena')->count();

            $labels[] = $date->format('D d/m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Desayunos',
                    'data' => $desayunosData,
                    'backgroundColor' => 'rgba(255, 193, 7, 0.8)',
                    'borderColor' => 'rgb(255, 193, 7)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Almuerzos',
                    'data' => $almuerzosData,
                    'backgroundColor' => 'rgba(40, 167, 69, 0.8)',
                    'borderColor' => 'rgb(40, 167, 69)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Meriendas',
                    'data' => $meriendasData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.8)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Cenas',
                    'data' => $cenasData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
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
