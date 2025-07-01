<?php

namespace App\Filament\Widgets;

use App\Models\Comida;
use App\Models\personal;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ComidasPorDepartamentoChart extends ChartWidget
{
    protected static ?string $heading = 'Comidas por Departamento';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Obtener todos los departamentos
        $departamentos = personal::whereNotNull('departamento')
            ->distinct()
            ->pluck('departamento')
            ->toArray();

        // Si no hay departamentos definidos, usar algunos por defecto
        if (empty($departamentos)) {
            $departamentos = ['Administración', 'Producción', 'Logística', 'Ventas', 'Recursos Humanos', 'TI', 'Otro'];
        }

        // Calcular las comidas por departamento para el mes actual
        $comidasPorDepartamento = [];
        $totalComidasMes = 0;

        foreach ($departamentos as $departamento) {
            // Obtener los IDs de personal en este departamento
            $personalIds = personal::where('departamento', $departamento)
                ->pluck('nro_identificacion')
                ->toArray();

            // Contar comidas para este departamento en el mes actual
            $cantidadComidas = Comida::whereIn('codigo', $personalIds)
                ->whereMonth('fecha', Carbon::now()->month)
                ->whereYear('fecha', Carbon::now()->year)
                ->count();

            $comidasPorDepartamento[$departamento] = $cantidadComidas;
            $totalComidasMes += $cantidadComidas;
        }

        // Calcular porcentajes para el gráfico
        $data = [];
        $labels = [];
        $backgroundColors = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
        ];

        // Asegurarse de que hay suficientes colores
        while (count($backgroundColors) < count($departamentos)) {
            $backgroundColors = array_merge($backgroundColors, $backgroundColors);
        }

        // Preparar datos para el gráfico
        foreach ($departamentos as $index => $departamento) {
            $cantidad = $comidasPorDepartamento[$departamento] ?? 0;

            if ($cantidad > 0) {
                $data[] = $cantidad;
                $labels[] = $departamento;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Comidas',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderColor' => 'rgba(55, 65, 81, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label + ': ' + context.parsed + ' comidas';
                        }",
                    ],
                ],
            ],
        ];
    }
}
