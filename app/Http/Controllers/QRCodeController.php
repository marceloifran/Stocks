<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use App\Models\personal;
use App\Models\asistencia;
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

public function verificarRegistros(Request $request)
{
    try {
        $codigo = $request->input('codigo');
        $fecha = $request->input('fecha');

        // Realizar la consulta para contar los registros
        $cantidadRegistros = asistencia::where('codigo', $codigo)
            ->where('fecha', $fecha)
            ->count();
        return response()->json(['cantidad' => $cantidadRegistros], 200);
    } catch (\Exception $e) {
        Log::error('Error al verificar la cantidad de registros: ' . $e->getMessage());
        return response()->json(['message' => 'Error al verificar la cantidad de registros'], 500);
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

}
