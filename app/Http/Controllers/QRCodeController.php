<?php

namespace App\Http\Controllers;

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
//eliminar a lazaro a mama a lopez yanina, cardona gonzalo, yamila villaroel
            // miralpeix manuel, ayarde david, zamora mateo,sarries, urribarri

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
// use Carbon\Carbon;

public function guardarAsistencia(Request $request)
{
    try {
        // Crear un array para almacenar los datos de asistencia
        $asistenciaData = [];

        foreach ($request->asistencia as $item) {
            $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');

            // Agregar los datos de asistencia al array
            $asistenciaData[] = [
                'codigo' => $item['codigo'],
                'fecha' => $fecha,
                'hora' => $item['hora'],
                'estado' => $item['estado'], // Ajusta esto según tus necesidades
            ];
        }

        // Realizar un solo insert masivo
        Asistencia::insert($asistenciaData);

        \Log::info('Asistencia guardada: ' . json_encode($asistenciaData));

        // Respuesta exitosa
        return response()->json(['message' => 'Asistencia guardada exitosamente'], 200);
    } catch (\Exception $e) {
        // Error en el servidor
        \Log::error('Error al guardar la asistencia: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()]);
    }
}



public function dia()
{
    $asistencia = Asistencia::where('fecha', Carbon::now()->format('Y-m-d'))->get();
    return view('asistenciaVer', compact('asistencia'));
}

public function semana()
{
    $asistencia = Asistencia::whereBetween('fecha', [
        Carbon::now()->startOfWeek()->format('Y-m-d'), // Fecha de inicio de la semana
        Carbon::now()->endOfWeek()->format('Y-m-d') // Fecha de fin de la semana
    ])->get();

    return view('asistenciaVer', compact('asistencia'));
}

public function mes()
{
    $asistencia = Asistencia::all();
    return view('asistenciaVer', compact('asistencia'));
}







}
