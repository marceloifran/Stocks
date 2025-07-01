<?php

namespace App\Filament\Widgets;

use App\Models\asistencia;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class HorasChart extends ChartWidget
{
    protected static ?string $heading = 'Horas Trabajadas';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Calcular las horas normales y extras del mes actual
        $horasNormales = 0;
        $horasExtras = 0;

        // Obtener todas las asistencias del mes actual
        $asistencias = asistencia::whereMonth('fecha', Carbon::now()->month)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $entrada = null;
        foreach ($asistencias as $asistencia) {
            if ($asistencia->estado == 'entrada') {
                $entrada = Carbon::parse($asistencia->fecha . ' ' . $asistencia->hora);
            } elseif ($asistencia->estado == 'salida' && $entrada) {
                $salida = Carbon::parse($asistencia->fecha . ' ' . $asistencia->hora);
                $fechaSalida = Carbon::parse($asistencia->fecha);

                // Verificar si es fin de semana
                $esFinDeSemana = $fechaSalida->isWeekend();

                // Definir hora límite (18:00)
                $horaLimite = Carbon::parse($asistencia->fecha . ' 18:00:00');

                // Calcular horas normales y extras
                if ($esFinDeSemana) {
                    // Si es fin de semana, todas las horas son extras
                    $horasExtras += round($salida->diffInMinutes($entrada) / 60, 2);
                } else {
                    // Si es día de semana
                    if ($salida->lte($horaLimite)) {
                        // Si la salida es antes de las 18:00, todas son horas normales
                        $horasNormales += round($salida->diffInMinutes($entrada) / 60, 2);
                    } else {
                        // Si la salida es después de las 18:00
                        if ($entrada->lte($horaLimite)) {
                            // Entrada antes de las 18:00
                            $horasNormales += round($horaLimite->diffInMinutes($entrada) / 60, 2);
                            $horasExtras += round($salida->diffInMinutes($horaLimite) / 60, 2);
                        } else {
                            // Entrada después de las 18:00, todas son horas extras
                            $horasExtras += round($salida->diffInMinutes($entrada) / 60, 2);
                        }
                    }
                }

                $entrada = null; // Reset entrada after processing
            }
        }

        return [
            'datasets' => [
                [
                    'data' => [round($horasNormales, 0), round($horasExtras, 0)],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Horas Normales', 'Horas Extras'],
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
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label + ': ' + context.raw + ' horas';
                        }",
                    ],
                ],
            ],
        ];
    }
}
