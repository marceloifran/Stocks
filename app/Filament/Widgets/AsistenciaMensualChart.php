<?php

namespace App\Filament\Widgets;

use App\Models\asistencia;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AsistenciaMensualChart extends ChartWidget
{
    protected static ?string $heading = 'Asistencia Mensual';
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Obtener los últimos 6 meses
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        // Obtener la cantidad de asistencias por mes
        $asistencias = asistencia::whereBetween('fecha', [
            Carbon::now()->subMonths(5)->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])
            ->where('estado', 'entrada')
            ->select(
                DB::raw('YEAR(fecha) as year'),
                DB::raw('MONTH(fecha) as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('year', 'month')
            ->get();

        // Preparar los datos para el gráfico
        $labels = $months->map(function ($month) {
            return $month->format('M Y');
        })->toArray();

        $data = $months->map(function ($month) use ($asistencias) {
            $monthData = $asistencias->where('year', $month->year)
                ->where('month', $month->month)
                ->first();
            return $monthData ? $monthData->count : 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Asistencias',
                    'data' => $data,
                    'backgroundColor' => ['#4BC0C0', '#36A2EB', '#FFCE56', '#FF6384', '#9966FF', '#FF9F40'],
                    'borderColor' => '#4BC0C0',
                    'fill' => false,
                    'tension' => 0.1,
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
