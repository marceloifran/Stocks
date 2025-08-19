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

        // Generar PDF
        $pdf = Pdf::loadView('reports.huella-carbono', [
            'totalEmisiones' => $totalEmisiones,
            'estadisticas' => $estadisticas,
            'porcentajes' => $porcentajes,
            'registros' => $huellasCarbono,
            'emisionesPorDia' => $emisionesPorDia,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'periodo' => $periodo,
            'tituloPeriodo' => $tituloPeriodo,
        ]);

        // Establecer opciones para que el PDF se abra directamente
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        // Mostrar el PDF en el navegador
        return $pdf->stream("reporte-huella-carbono-{$periodo}.pdf");
    }
}
