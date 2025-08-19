<?php

namespace App\Http\Controllers;

use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HuellaCarbonoReportController extends Controller
{
    public function generateReport(Request $request)
    {
        // Obtener el periodo seleccionado (semana, quincena, mes o todo)
        $periodo = $request->input('periodo', 'mes');

        // Determinar el rango de fechas según el periodo
        $fechaFin = Carbon::now();
        $fechaInicio = null;

        switch ($periodo) {
            case 'semana':
                $fechaInicio = Carbon::now()->subDays(7);
                $tituloPeriodo = 'Últimos 7 días';
                break;
            case 'quincena':
                $fechaInicio = Carbon::now()->subDays(15);
                $tituloPeriodo = 'Últimos 15 días';
                break;
            case 'mes':
                $fechaInicio = Carbon::now()->subDays(30);
                $tituloPeriodo = 'Últimos 30 días';
                break;
            default:
                $tituloPeriodo = 'Histórico completo';
                break;
        }

        // Preparar la consulta base
        $query = HuellaCarbono::with('detalles');

        // Aplicar filtro de fecha si corresponde
        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio)
                ->where('fecha', '<=', $fechaFin);
        }

        // Obtener los registros
        $huellasCarbono = $query->orderBy('fecha', 'desc')->get();

        // Calcular totales y estadísticas
        $totalEmisiones = $huellasCarbono->sum('total_emisiones');

        // Usar estadísticas de la URL si están disponibles
        $estadisticasUrl = $request->input('estadisticas');
        if ($estadisticasUrl && $periodo === 'todo') {
            $estadisticas = json_decode($estadisticasUrl, true);
        } else {
            // Calcular estadísticas por tipo de fuente
            $estadisticas = [
                'combustible' => 0,
                'electricidad' => 0,
                'residuos' => 0,
            ];

            foreach ($huellasCarbono as $huella) {
                foreach ($huella->detalles as $detalle) {
                    if (isset($detalle->detalles['categoria'])) {
                        $categoria = $detalle->detalles['categoria'];
                        if (isset($estadisticas[$categoria])) {
                            $estadisticas[$categoria] += $detalle->emisiones_co2;
                        }
                    }
                }
            }
        }

        // Calcular porcentajes
        $porcentajes = [];
        foreach ($estadisticas as $tipo => $valor) {
            $porcentajes[$tipo] = $totalEmisiones > 0 ? round(($valor / $totalEmisiones) * 100, 1) : 0;
        }

        // Agrupar datos por día para el gráfico
        $emisionesPorDia = $huellasCarbono
            ->groupBy(function ($item) {
                return Carbon::parse($item->fecha)->format('Y-m-d');
            })
            ->map(function ($group) {
                return [
                    'fecha' => Carbon::parse($group->first()->fecha)->format('d/m/Y'),
                    'emisiones' => $group->sum('total_emisiones'),
                ];
            })
            ->values();

        // Calcular tendencia
        $emisionesPorMes = [];
        if ($huellasCarbono->count() > 0) {
            $emisionesPorMes = $huellasCarbono
                ->groupBy(function ($item) {
                    return Carbon::parse($item->fecha)->format('Y-m');
                })
                ->map(function ($group) {
                    return [
                        'mes' => Carbon::parse($group->first()->fecha)->format('M Y'),
                        'emisiones' => $group->sum('total_emisiones'),
                    ];
                })
                ->sortBy(function ($item, $key) {
                    return $key;
                })
                ->values();
        }

        // Determinar si hay una tendencia de aumento o disminución
        $tendencia = 'estable';
        $porcentajeCambio = 0;

        if (count($emisionesPorMes) > 1) {
            $ultimoMes = $emisionesPorMes->last()['emisiones'];
            $penultimoMes = $emisionesPorMes[count($emisionesPorMes) - 2]['emisiones'];

            if ($penultimoMes > 0) {
                $porcentajeCambio = round((($ultimoMes - $penultimoMes) / $penultimoMes) * 100, 1);

                if ($porcentajeCambio > 5) {
                    $tendencia = 'aumento';
                } elseif ($porcentajeCambio < -5) {
                    $tendencia = 'disminución';
                }
            }
        }

        // Generar PDF
        $pdf = Pdf::loadView('reports.huella-carbono', [
            'totalEmisiones' => $totalEmisiones,
            'estadisticas' => $estadisticas,
            'porcentajes' => $porcentajes,
            'registros' => $huellasCarbono,
            'emisionesPorDia' => $emisionesPorDia,
            'emisionesPorMes' => $emisionesPorMes,
            'tendencia' => $tendencia,
            'porcentajeCambio' => $porcentajeCambio,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'periodo' => $periodo,
            'tituloPeriodo' => $tituloPeriodo,
            'empresa' => 'Comprehensive Management System',
            'fechaInicio' => $fechaInicio ? $fechaInicio->format('d/m/Y') : 'Inicio de operaciones',
            'fechaFin' => $fechaFin->format('d/m/Y'),
        ]);

        // Establecer opciones para que el PDF se vea mejor
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Helvetica',
            'dpi' => 150,
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
        ]);

        // Mostrar el PDF en el navegador
        return $pdf->stream("informe-huella-carbono-{$periodo}.pdf");
    }
}
