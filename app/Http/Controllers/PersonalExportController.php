<?php

namespace App\Http\Controllers;

use App\Models\permiso;
use App\Models\personal;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PersonalExportController extends Controller
{
    public function exportPdf($record)
{
    // Obtener la persona y sus movimientos de stock
    $persona = personal::with('stockMovement')->findOrFail($record);

    // Decodificar la firma Base64 y guardarla como archivo temporal
    $firmaBase64 = $persona->stockMovement->first()->firma; // Asumiendo que la firma está en el primer movimiento
    $firmaData = substr($firmaBase64, strpos($firmaBase64, ',') + 1);
    $firma = base64_decode($firmaData);
    $firmaPath = storage_path('app/temp_firma.png');
    file_put_contents($firmaPath, $firma);

    // Crear una instancia de PDF
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');

    // Generar el PDF utilizando la vista personalizada y pasando la ruta de la firma
    $pdf->loadView('persona', compact('persona', 'firmaPath'));

    // Descargar el PDF
    return $pdf->download("persona_{$persona->id}.pdf");
}

public function exportReporte($record)
{
    // Obtener la persona y sus movimientos de stock
    $permiso = permiso::with('personal')->findOrFail($record);

    // Decodificar la firma Base64 y guardarla como archivo temporal
    $firmaBase64 = $permiso->personal->first()->firma; // Asumiendo que la firma está en el primer movimiento
    $firmaData = substr($firmaBase64, strpos($firmaBase64, ',') + 1);
    $firma = base64_decode($firmaData);
    $firmaPath = storage_path('app/temp_firma.png');
    file_put_contents($firmaPath, $firma);

    // Crear una instancia de PDF
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');

    // Generar el PDF utilizando la vista personalizada y pasando la ruta de la firma
    $pdf->loadView('permiso', compact('permiso', 'firmaPath'));

    // Descargar el PDF
    return $pdf->download("permiso_{$permiso->tipo}.pdf");
}

public function pdfpersonal ()
{
    $personal = personal::all();
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('pdfpersonal', compact('personal'));
    return $pdf->download("personal.pdf");
}

public function porcentajeasis($record)
{
    $totalRegistros = count($record);

    // Calcula el número de asistencias (registros con 'asistencia' en true)
    $asistencias = collect($record)->where('asistencia', true)->count();

    // Calcula el número de faltas restando el total de registros menos las asistencias
    $faltas = $totalRegistros - $asistencias;

    // Calcula el porcentaje de asistencia y falta
    $porcentajeAsistencia = ($asistencias / $totalRegistros) * 100;
    $porcentajeFalta = ($faltas / $totalRegistros) * 100;

    // Devuelve un array con los porcentajes de asistencia y falta
    return [
        'porcentajeAsistencia' => number_format($porcentajeAsistencia, 2) . '%',
        'porcentajeFalta' => number_format($porcentajeFalta, 2) . '%',
    ];
}

public function exportPorcentajePdf($record)
{
    // Obtener los porcentajes de asistencia y falta
    $porcentajes = $this->porcentajeasis($record);

    // Crear una instancia de PDF
    $pdf = PDF::loadView('porcentaje_pdf', compact('porcentajes'));

    // Descargar el PDF
    return $pdf->download("porcentaje_asistencia.pdf");
}

}
