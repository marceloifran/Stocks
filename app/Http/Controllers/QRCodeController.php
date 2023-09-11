<?php

namespace App\Http\Controllers;

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
        $numeros = $request->input('numeros');

        // Realiza una consulta en la base de datos para buscar coincidencias
        $coincidencias = personal::whereIn('nro_identificacion', [$numeros])->get();

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
            $asistencia = asistencia::create([
                'codigo' => $item['codigo'],
                'fecha' => $item['fecha'],
                'hora' => $item['hora'],
                'estado' => $item['estado'],
            ]);
            \Log::info('Asistencia guardada: ' . json_encode($asistencia));
        }

        // Respuesta exitosa
        return response()->json(['message' => 'Asistencia guardada exitosamente'], 200);
    } catch (\Exception $e) {
        // Error en el servidor
        \Log::error('Error al guardar la asistencia: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()]);
    }
}



}
