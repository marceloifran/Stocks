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

    public function generatesueldo($record)
    {
        // Recuperar el registro de sueldo
        $sueldo = Sueldo::findOrFail($record);

        // Cargar la vista para el PDF
        $pdf = Pdf::loadView('pdf.sueldo', ['sueldo' => $sueldo]);

        // Descargar el archivo PDF
        return $pdf->download('sueldo_comprobante_'  . '.pdf');
     
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
    $pdf->loadView('asistenciaVer', compact('asistenciaCombinada', 'totalPersonal','totalPresentes','totalAusentes'));

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

            // Calcular las horas normales y extras
            $horaLimite = Carbon::parse($asistencia->fecha . ' 19:00');
            if ($entrada->lessThanOrEqualTo($horaLimite)) {
                if ($salida->lessThanOrEqualTo($horaLimite)) {
                    $horasNormales = $salida->diffInMinutes($entrada) / 60;
                    $horasExtras = 0;
                } else {
                    $horasNormales = $horaLimite->diffInMinutes($entrada) / 60;
                    $horasExtras = $salida->diffInMinutes($horaLimite) / 60;
                }
            } else {
                $horasNormales = 0;
                $horasExtras = $salida->diffInMinutes($entrada) / 60;
            }

            $totalHorasNormales += $horasNormales;
            $totalHorasExtras += $horasExtras;

            $asistenciaCombinada[] = [
                'fecha' => $asistencia->fecha,
                'entrada' => $entrada->format('H:i'),
                'salida' => $salida->format('H:i'),
                'estado' => $asistencia->estado,
                'horas_normales' => $horasNormales,
                'horas_extras' => $horasExtras,
            ];

            $entrada = null; // Reset entrada after processing
        }
    }

    $totalAsistencias = $asistencias->where('presente', 1)->where('estado', 'entrada')->count();

    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('asistenciaPersonal', compact('asistenciaCombinada', 'persona', 'totalAsistencias', 'totalHorasNormales', 'totalHorasExtras'));

    return $pdf->download("asistencia.pdf");
}


}
