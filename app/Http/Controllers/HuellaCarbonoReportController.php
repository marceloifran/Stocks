<?php

namespace App\Http\Controllers;

use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HuellaCarbonoReportController extends Controller
{
    public function generateReport(Request $request)
    {
        // Obtener datos para el reporte
        $totalEmisiones = $request->input('totalEmisiones', 0);
        $estadisticas = json_decode($request->input('estadisticas', '{}'), true);

        // Si no hay datos en la URL, calcularlos
        if ($totalEmisiones == 0) {
            $huellasCarbono = HuellaCarbono::with('detalles')->get();
            $totalEmisiones = $huellasCarbono->sum('total_emisiones');

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

        // Obtener datos adicionales para el reporte
        $ultimosRegistros = HuellaCarbono::with('detalles')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get();

        // Generar PDF
        $pdf = Pdf::loadView('reports.huella-carbono', [
            'totalEmisiones' => $totalEmisiones,
            'estadisticas' => $estadisticas,
            'porcentajes' => $porcentajes,
            'ultimosRegistros' => $ultimosRegistros,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->stream('reporte-huella-carbono.pdf');
    }
}
