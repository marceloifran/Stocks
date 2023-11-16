<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use App\Models\personal;
use App\Models\asistencia;
use App\Models\HorasGenerales;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;




class QRCodeController extends Controller
{
    public function generateBulkQRs()
    {
        $personal = personal::all();
$contador = 1; // Inicializa un contador en 1

foreach ($personal as $person) {
    // Asigna el valor actual del contador al campo "nro_identificacion" del modelo personal
    $person->nro_identificacion = $contador;
    $person->save();

    // Incrementa el contador para la siguiente persona
    $contador++;
}

// Mensaje de éxito
session()->flash('success', 'Identificadores generados exitosamente.');

    }

    public function iniciarAsistencia ()
    {
        return view('asistencia');
    }
    public function iniciarhoras ()
    {
        return view('horas');
    }

public function buscar(Request $request)
{
    try {
        $codigo = $request->input('codigo');

        // Realiza una consulta en la base de datos para buscar coincidencias
        $coincidencias = personal::whereIn('nro_identificacion', [$codigo])->get();

        // Devuelve una respuesta JSON con las coincidencias
        return response()->json(['coincidencias' => $coincidencias]);

    } catch (QueryException $e) {
        // En caso de error en la consulta
        return response()->json(['error' => 'Error en la consulta'], 500);
    } catch (\Exception $e) {
        // En caso de otras excepciones generales
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}
public function buscarHoras(Request $request)
{
    try {
        $codigo = $request->input('codigo');

        // Realiza una consulta en la base de datos para buscar coincidencias
        $coincidencias = personal::whereIn('nro_identificacion', [$codigo])->get();

        // Devuelve una respuesta JSON con las coincidencias
        return response()->json(['coincidencias' => $coincidencias]);

    } catch (QueryException $e) {
        // En caso de error en la consulta
        return response()->json(['error' => 'Error en la consulta'], 500);
    } catch (\Exception $e) {
        // En caso de otras excepciones generales
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}
public function guardarAsistencia(Request $request)

{
    try {
        foreach ($request->asistencia as $item) {
            $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');
            $asistencia = Asistencia::create([
                'codigo' => $item['codigo'],
                'fecha' => $fecha,
                'hora' => $item['hora'],
                'estado' => $item['estado'],
                'presente' =>true
            ]);
}
        Log::error('Asistencia guardada: ' . json_encode($asistencia));

        // Respuesta exitosa
        return response()->json(['message' => 'Asistencia guardada exitosamente'], 200);
    } catch (\Exception $e) {
        Log::error('Error al guardar asistencia: ' . $e->getMessage());
        return response()->json(['message' => 'Error al guardar asistencia'], 500);
    }

}
public function guardarHoras(Request $request)

{
    try {
        foreach ($request->asistencia as $item) {
            $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');
            $asistencia = HorasGenerales::create([
                'codigo' => $item['codigo'],
                'fecha' => $fecha,
                'hora' => $item['hora'],
                'tipo' => $item['tipo'],
                'presente' =>true
            ]);
}
        Log::error('Asistencia guardada: ' . json_encode($asistencia));

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
    //aca toma tanto entrada como salida , preguntar a laza si lo quiere como esta o que solo muestre los de entrada.

    $totalAusentes = $totalPersonal - $totalPresentes;

      // Crear un array para almacenar la asistencia combinada de entrada y salida por empleado
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

    Log::error($asistenciaCombinada);

    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('asistenciaVer', compact('asistenciaCombinada', 'totalPersonal','totalPresentes','totalAusentes'));

    return $pdf->download("asistencia.pdf");
}




public function personal($record)
{
    $persona = Personal::find($record);

    // Si la persona no se encuentra, puedes manejar el error aquí
    if (!$persona) {
        return response()->json(['message' => 'Persona no encontrada'], 404);
    }

    // Obtener todas las asistencias de la persona
    $asistencias = Asistencia::where('codigo', $persona->nro_identificacion)->get();

    // ...

    // Crear un array para almacenar las asistencias combinadas
    $asistenciaCombinada = [];

    // Llenar $asistenciasCombinadas con los datos necesarios
    foreach ($asistencias as $asistencia) {
        $asistenciaCombinada[] = [
            'fecha' => $asistencia->fecha,
            'hora' => $asistencia->hora,
            'estado' => $asistencia->estado,
            // Otros datos de la asistencia si es necesario
        ];
    }
    $totalAsistencias = $asistencias->where('presente', 1)->count();



    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('asistenciaPersonal', compact('asistenciaCombinada','persona','totalAsistencias'));

    return $pdf->download("asistencia.pdf");
}
public function horasTrabajadasPorMes($record)
{
    $persona = Personal::find($record);
    $asistencias = Asistencia::where('codigo', $persona->nro_identificacion)
                    ->orderBy('fecha')
                    ->orderBy('hora')
                    ->get();

    $horasTrabajadasPorMes = [];
    $horasExtrasPorMes = [];

    foreach ($asistencias as $asistencia) {
        $hora = Carbon::parse($asistencia->fecha . ' ' . $asistencia->hora);
        $mesAnio = $hora->format('Y-m');

        if ($asistencia->estado === 'entrada') {
            $entrada = $hora;
        } elseif ($asistencia->estado === 'salida' && isset($entrada)) {
            // Calcular la diferencia en horas redondeando
            $diferenciaHoras = $entrada->diffInMinutes($hora) >= 30
                ? ceil($entrada->floatDiffInHours($hora))
                : floor($entrada->floatDiffInHours($hora));

            if (!isset($horasTrabajadasPorMes[$mesAnio])) {
                $horasTrabajadasPorMes[$mesAnio] = 0;
                $horasExtrasPorMes[$mesAnio] = 0;
            }

            // Verificar si es hora normal o extra según el día de la semana
            if ($hora->dayOfWeek >= Carbon::MONDAY && $hora->dayOfWeek <= Carbon::THURSDAY) {
                // Es un día de lunes a jueves, considerar 9 horas como normales
                $horasTrabajadasPorMes[$mesAnio] += min($diferenciaHoras, 9);
                $horasExtrasPorMes[$mesAnio] += max(0, $diferenciaHoras - 9);
            } else {
                // Es viernes, considerar 8 horas como normales
                $horasTrabajadasPorMes[$mesAnio] += min($diferenciaHoras, 8);
                $horasExtrasPorMes[$mesAnio] += max(0, $diferenciaHoras - 8);
            }

            unset($entrada);
        }
    }

    return view('horas-trabajadas-por-mes', compact('horasTrabajadasPorMes', 'horasExtrasPorMes'));
}







public function horas()
{
    $personal = personal::all();
    // $asistencia = asistencia::where('fecha', Carbon::now());
    $asistencia = HorasGenerales::whereDate('fecha', Carbon::today())->get();



    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('horasVer', compact('asistencia'));

    return $pdf->download("asistencia.pdf");
}


}
