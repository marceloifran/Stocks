<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sueldo;
use App\Models\personal;
use App\Models\asistencia;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use App\Models\HorasGenerales;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;




class QRCodeController extends Controller
{
    public function showQr($id)
    {
        $personal = personal::findOrFail($id);
        $qrCode = QrCode::size(300)->generate($personal->nro_identificacion);

        return view('personal.qr', compact('qrCode', 'personal'));
    }

    public function generatePdf($id)
    {
        $personal = personal::findOrFail($id);
        $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($personal->nro_identificacion));
        $pdf = Pdf::loadView('personal.pdf', compact('personal', 'qrCode'));

        return $pdf->download('personal_qr.pdf');
    }

    public function generateAllPdf()
    {
        $personals = personal::all();
        $pdf = Pdf::loadView('personal.all_pdf', compact('personals'));

        return $pdf->download('personals_qr.pdf');
    }


    public function iniciarAsistencia()
    {
        return view('asistencia');
    }
    public function iniciarhoras()
    {
        return view('horas');
    }

    public function buscar(Request $request)
    {
        try {
            $codigo = $request->input('codigo');

            // Registrar el código recibido para depuración
            Log::info('Código recibido para búsqueda: ' . $codigo);

            // Buscar coincidencias exactas primero
            $coincidencias = personal::where('nro_identificacion', $codigo)->get();

            // Si no hay coincidencias exactas, intentar buscar por ID
            if ($coincidencias->isEmpty()) {
                Log::info('No se encontraron coincidencias exactas, buscando por ID');
                $coincidencias = personal::where('id', $codigo)->get();
            }

            // Si sigue sin haber coincidencias, intentar buscar por DNI
            if ($coincidencias->isEmpty()) {
                Log::info('No se encontraron coincidencias por ID, buscando por DNI');
                $coincidencias = personal::where('dni', $codigo)->get();
            }

            // Registrar resultado de la búsqueda
            Log::info('Coincidencias encontradas: ' . $coincidencias->count());

            return response()->json(['coincidencias' => $coincidencias]);
        } catch (QueryException $e) {
            Log::error('Error en la consulta de búsqueda: ' . $e->getMessage());
            return response()->json(['error' => 'Error en la consulta'], 500);
        } catch (\Exception $e) {
            Log::error('Error interno al buscar coincidencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function guardarAsistencia(Request $request)
    {
        try {
            foreach ($request->asistencia as $item) {
                // Convertir la fecha al formato Y-m-d
                $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');

                // Buscar un registro existente con el mismo código, fecha y estado
                $asistenciaExistente = Asistencia::where('codigo', $item['codigo'])
                    ->where('fecha', $fecha)
                    ->where('estado', $item['estado'])
                    ->first();

                if (!$asistenciaExistente) {
                    Asistencia::create([
                        'codigo' => $item['codigo'],
                        'fecha' => $fecha,
                        'hora' => $item['hora'],
                        'estado' => $item['estado'],
                        'presente' => true
                    ]);
                }
            }

            // Respuesta exitosa
            return response()->json(['message' => 'Asistencia guardada exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error al guardar asistencia: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar asistencia'], 500);
        }
    }


    public function dia()
    {
        $personal = personal::all();
        // $asistencia = asistencia::where('fecha', Carbon::now());
        $asistencia = asistencia::whereDate('fecha', Carbon::today())->get();
        $totalPersonal = personal::count();

        //  $totalPresentes = $asistencia->where('estado', 'entrada');
        $totalPresentes = $asistencia->whereNotNull('presente')->where('estado', 'entrada')->count();
        $totalAusentes = $totalPersonal - $totalPresentes;

        $asistenciaCombinada = [];

        foreach ($personal as $empleado) {
            $entrada = $asistencia->where('codigo', $empleado->nro_identificacion)
                ->where('estado', 'entrada')
                ->first();

            $salida = $asistencia->where('codigo', $empleado->nro_identificacion)
                ->where('estado', 'salida')
                ->first();

            $asistenciaCombinada[] = [
                'empleado' => $empleado,
                'entrada' => $entrada,
                'salida' => $salida,
            ];
        }

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');
        $pdf->loadView('asistenciaVer', compact('asistenciaCombinada', 'totalPersonal', 'totalPresentes', 'totalAusentes'));

        return $pdf->download("asistencia.pdf");
    }

    public function personal($record)
    {
        $persona = Personal::find($record);

        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        $asistencias = Asistencia::where('codigo', $persona->nro_identificacion)
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $asistenciaCombinada = [];
        $totalHorasNormales = 0;
        $totalHorasExtras = 0;
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
                    $horasNormales = 0;
                    $horasExtras = round($salida->diffInMinutes($entrada) / 60, 2);
                } else {
                    // Si es día de semana
                    if ($salida->lte($horaLimite)) {
                        // Si la salida es antes de las 18:00, todas son horas normales
                        $horasNormales = round($salida->diffInMinutes($entrada) / 60, 2);
                        $horasExtras = 0;
                    } else {
                        // Si la salida es después de las 18:00
                        if ($entrada->lte($horaLimite)) {
                            // Entrada antes de las 18:00
                            $horasNormales = round($horaLimite->diffInMinutes($entrada) / 60, 2);
                            $horasExtras = round($salida->diffInMinutes($horaLimite) / 60, 2);
                        } else {
                            // Entrada después de las 18:00, todas son horas extras
                            $horasNormales = 0;
                            $horasExtras = round($salida->diffInMinutes($entrada) / 60, 2);
                        }
                    }
                }

                $totalHorasNormales += $horasNormales;
                $totalHorasExtras += $horasExtras;

                $asistenciaCombinada[] = [
                    'fecha' => $asistencia->fecha,
                    'entrada' => $entrada->format('H:i'),
                    'salida' => $salida->format('H:i'),
                    'horas_normales' => $horasNormales,
                    'horas_extras' => $horasExtras,
                    'es_fin_de_semana' => $esFinDeSemana,
                ];

                $entrada = null; // Reset entrada after processing
            }
        }

        // Agrupar asistencias por día y sumar horas
        $asistenciasPorDia = collect($asistenciaCombinada)->groupBy('fecha')->map(function ($group) {
            return [
                'horas_normales' => round($group->sum('horas_normales'), 2),
                'horas_extras' => round($group->sum('horas_extras'), 2),
            ];
        });

        // Agrupar asistencias por semana
        $asistenciasPorSemana = collect($asistenciaCombinada)->groupBy(function ($asistencia) {
            return Carbon::parse($asistencia['fecha'])->format('W'); // Agrupa por semana del año
        })->map(function ($group) {
            return [
                'horas_normales' => round($group->sum('horas_normales'), 2),
                'horas_extras' => round($group->sum('horas_extras'), 2),
            ];
        });

        // Agrupar asistencias por quincena
        $asistenciasPorQuincena = collect($asistenciaCombinada)->groupBy(function ($asistencia) {
            $fecha = Carbon::parse($asistencia['fecha']);
            return $fecha->day <= 15 ? 'Primera Quincena' : 'Segunda Quincena';
        })->map(function ($group) {
            return [
                'horas_normales' => round($group->sum('horas_normales'), 2),
                'horas_extras' => round($group->sum('horas_extras'), 2),
            ];
        });

        $totalAsistencias = $asistencias->where('presente', 1)->where('estado', 'entrada')->count();

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');
        $pdf->loadView('asistenciaPersonal', compact('asistenciaCombinada', 'persona', 'totalAsistencias', 'totalHorasNormales', 'totalHorasExtras', 'asistenciasPorDia', 'asistenciasPorSemana', 'asistenciasPorQuincena'));

        return $pdf->download("asistencia.pdf");
    }
}
