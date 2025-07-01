<?php

namespace App\Filament\Widgets;

use App\Models\Comida;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ComidasChart extends ChartWidget
{
    protected static ?string $heading = 'Comidas';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Obtener la cantidad de comidas por tipo para el mes actual
        $comidas = Comida::whereMonth('fecha', Carbon::now()->month)
            ->select('tipo_comida', DB::raw('count(*) as count'))
            ->groupBy('tipo_comida')
            ->get();

        // Preparar los datos para el gráfico
        $labels = ['Desayuno', 'Almuerzo', 'Merienda', 'Cena'];

        $data = [
            $comidas->where('tipo_comida', 'desayuno')->first()->count ?? 0,
            $comidas->where('tipo_comida', 'almuerzo')->first()->count ?? 0,
            $comidas->where('tipo_comida', 'merienda')->first()->count ?? 0,
            $comidas->where('tipo_comida', 'cena')->first()->count ?? 0,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Comidas',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    'borderWidth' => 1,
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
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'cutout' => '70%',
        ];
    }
}
