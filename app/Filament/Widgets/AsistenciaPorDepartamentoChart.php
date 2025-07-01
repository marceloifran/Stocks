<?php

namespace App\Filament\Widgets;

use App\Models\asistencia;
use App\Models\personal;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AsistenciaPorDepartamentoChart extends ChartWidget
{
    protected static ?string $heading = 'Asistencia por Departamento';
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

        // Calcular la asistencia por departamento para el mes actual
        $data = [];
        $totalPorDepartamento = [];
        $asistenciaPorDepartamento = [];

        foreach ($departamentos as $departamento) {
            // Total de personal en el departamento
            $totalPersonal = personal::where('departamento', $departamento)->count();
            $totalPorDepartamento[$departamento] = max(1, $totalPersonal); // Evitar división por cero

            // Días laborables en el mes actual (aproximado)
            $diasLaborables = Carbon::now()->daysInMonth - 8; // Restando fines de semana

            // Asistencias esperadas para el departamento en el mes
            $asistenciasEsperadas = $totalPersonal * $diasLaborables;

            // Asistencias reales del departamento en el mes
            $personalIds = personal::where('departamento', $departamento)
                ->pluck('nro_identificacion')
                ->toArray();

            $asistenciasReales = asistencia::whereIn('codigo', $personalIds)
                ->whereMonth('fecha', Carbon::now()->month)
                ->where('estado', 'entrada')
                ->count();

            // Calcular porcentaje de asistencia
            $porcentaje = $asistenciasEsperadas > 0
                ? round(($asistenciasReales / $asistenciasEsperadas) * 100, 1)
                : 0;

            $asistenciaPorDepartamento[$departamento] = $porcentaje;
            $data[] = $porcentaje;
        }

        // Definir colores según el porcentaje de asistencia
        $backgroundColors = array_map(function ($porcentaje) {
            if ($porcentaje >= 90) {
                return 'rgba(52, 211, 153, 0.8)'; // Verde para alta asistencia
            } elseif ($porcentaje >= 70) {
                return 'rgba(251, 191, 36, 0.8)'; // Amarillo para asistencia media
            } else {
                return 'rgba(239, 68, 68, 0.8)'; // Rojo para baja asistencia
            }
        }, $asistenciaPorDepartamento);

        return [
            'datasets' => [
                [
                    'label' => 'Porcentaje de Asistencia',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => 'rgba(55, 65, 81, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $departamentos,
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
                    'max' => 100,
                    'ticks' => [
                        'callback' => "function(value) { return value + '%'; }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.parsed.y + '% de asistencia';
                        }",
                    ],
                ],
            ],
        ];
    }
}
