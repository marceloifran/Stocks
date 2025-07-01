<?php

namespace App\Filament\Widgets;

use App\Models\asistencia;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AsistenciaChart extends ChartWidget
{
    protected static ?string $heading = 'Asistencia';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Obtener las fechas de la última semana
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // Obtener la cantidad de asistencias por día
        $asistencias = asistencia::whereIn(DB::raw('DATE(fecha)'), $dates->toArray())
            ->where('estado', 'entrada')
            ->select(DB::raw('DATE(fecha) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Preparar los datos para el gráfico
        $labels = $dates->map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        })->toArray();

        $data = $dates->map(function ($date) use ($asistencias) {
            return $asistencias->get($date)->count ?? 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Asistencias',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => '#36A2EB',
                    'pointBackgroundColor' => '#36A2EB',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => '#36A2EB',
                    'fill' => true,
                    'tension' => 0.4,
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
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
}
