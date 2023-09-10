<?php

namespace App\Http\Controllers;

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

}
