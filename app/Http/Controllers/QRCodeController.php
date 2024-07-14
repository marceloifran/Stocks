<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use App\Models\personal;
use App\Models\asistencia;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use App\Models\HorasGenerales;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
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

        $coincidencias = personal::whereIn('nro_identificacion', [$codigo])->get();

        return response()->json(['coincidencias' => $coincidencias]);

    } catch (QueryException $e) {
        return response()->json(['error' => 'Error en la consulta'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}

public function guardarAsistencia(Request $request)
{
    try {
        foreach ($request->asistencia as $item) {
            $fecha = Carbon::createFromFormat('d/m/Y', $item['fecha'])->format('Y-m-d');

            // Buscar un registro existente con el mismo código, fecha y hora
            $asistenciaExistente = Asistencia::where('codigo', $item['codigo'])
                ->where('fecha', $fecha)
                ->where('hora', $item['hora'])
                ->first();

            // Si el registro ya existe, actualizarlo
            if ($asistenciaExistente) {
                $asistenciaExistente->update([
                    'estado' => $item['estado'],
                    'presente' => true
                ]);
            } else {
                // Si no existe, crear un nuevo registro
                $asistencia = Asistencia::create([
                    'codigo' => $item['codigo'],
                    'fecha' => $fecha,
                    'hora' => $item['hora'],
                    'estado' => $item['estado'],
                    'presente' => true
                ]);
            }
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
