<?php

namespace App\Http\Controllers;

use App\Models\asistencia;
use App\Models\personal;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;




class QRCodeController extends Controller
{
    public function generateBulkQRs()
    {
        $personal = Personal::all();

        foreach ($personal as $person) {
            // Genera un número aleatorio único de hasta 4 dígitos
            $randomIdentifier = mt_rand(1000, 9999);

            // Asigna el número aleatorio al campo "identificador" del modelo personal
            $person->nro_identificacion = $randomIdentifier;
            $person->save();

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
        // Validación de los datos de entrada (ajusta esto según tus necesidades)
        $request->validate([
            'codigo' => 'required|string', // Asume que el código se envía en la solicitud
            'fecha' => 'required|date',
            'hora' => 'required|time',
            'estado' => 'required|in:presente,no',
        ]);

        // Crea una nueva instancia de Asistencia y guárdala en la base de datos
        asistencia::create([
            'codigo' => $request->codigo,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => $request->estado,
        ]);

        // Respuesta exitosa
        return response()->json(['message' => 'Asistencia guardada exitosamente'], 200);
    } catch (\Exception $e) {
        // Error en el servidor
        return response()->json(['message' => 'Error al guardar la asistencia'], 500);
    }
}



}
