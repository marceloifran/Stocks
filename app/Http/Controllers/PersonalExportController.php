<?php

namespace App\Http\Controllers;

use App\Models\capacitaciones;
use App\Models\checklists;
use App\Models\entidad;
use App\Models\ingresos;
use App\Models\permiso;
use App\Models\personal;
use App\Models\stock;
use App\Models\StockHistory;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PersonalExportController extends Controller
{


public function pdfpersonal ()
{
    $personal = personal::all();
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('pdfpersonal', compact('personal'));
    return $pdf->download("personal.pdf");
}



public function exportPdf($record)
{
    // Obtener la persona y sus movimientos de stock
    $persona = personal::with('stockMovement')->findOrFail($record);

    // Obtener los datos de la entidad

    // Decodificar la firma Base64 y guardarla como archivo temporal
    $firmaBase64 = $persona->stockMovement->first()->firma;
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
}
