<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Comida;
use App\Models\personal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class ComidaController extends Controller
{
    public function iniciarComida()
    {
        return view('comida');
    }

    public function guardarComida(Request $request)
    {
        try {
            foreach ($request->comida as $item) {
                // Convertir la fecha al formato Y-m-d
                $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');

                // Buscar un registro existente con el mismo código, fecha y tipo_comida
                $comidaExistente = Comida::where('codigo', $item['codigo'])
                    ->where('fecha', $fecha)
                    ->where('tipo_comida', $item['tipo_comida'])
                    ->first();

                if (!$comidaExistente) {
                    Comida::create([
                        'codigo' => $item['codigo'],
                        'fecha' => $fecha,
                        'hora' => $item['hora'],
                        'tipo_comida' => $item['tipo_comida'],
                        'presente' => true
                    ]);
                }
            }

            // Respuesta exitosa
            return response()->json(['message' => 'Comida guardada exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error al guardar comida: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar comida'], 500);
        }
    }

    public function generarReporte()
    {
        // Obtener comidas del día actual
        $comidas = Comida::with('personal')
            ->whereDate('fecha', Carbon::today())
            ->orderBy('hora')
            ->get();

        $totalComidas = $comidas->count();

        // Agrupar comidas por tipo
        $comidasPorTipo = $comidas->groupBy('tipo_comida')
            ->map(function ($grupo) {
                return $grupo->count();
            });

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');
        $pdf->loadView('comidaReporte', compact('comidas', 'totalComidas', 'comidasPorTipo'));

        return $pdf->download("comidas_" . Carbon::today()->format('Y-m-d') . ".pdf");
    }

    public function reportePersonal($record)
    {
        $persona = personal::find($record);

        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        // Obtener todas las comidas de la persona
        $comidas = Comida::where('codigo', $persona->nro_identificacion)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Agrupar comidas por tipo
        $comidasPorTipo = $comidas->groupBy('tipo_comida')
            ->map(function ($grupo) {
                return $grupo->count();
            });

        // Agrupar comidas por día
        $comidasPorDia = $comidas->groupBy('fecha')
            ->map(function ($grupo) {
                return $grupo->groupBy('tipo_comida')
                    ->map(function ($tipoGrupo) {
                        return $tipoGrupo->count();
                    });
            });

        $totalComidas = $comidas->count();

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');
        $pdf->loadView('comidaReportePersonal', compact('comidas', 'persona', 'totalComidas', 'comidasPorTipo', 'comidasPorDia'));

        return $pdf->download("comidas_" . $persona->nombre . ".pdf");
    }
}
