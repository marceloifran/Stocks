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
    public function exportIngreso($record)
    {
        // Obtener la persona y sus movimientos de stock
        $persona = ingresos::findOrFail($record);

        // Decodificar la firma Base64 y guardarla como archivo temporal
        $firmaBase64 = $persona->firma;
        $firmaData = substr($firmaBase64, strpos($firmaBase64, ',') + 1);
        $firma = base64_decode($firmaData);
        $firmaPath = storage_path('app/temp_firma.png');
        file_put_contents($firmaPath, $firma);

       // Detalles de los elementos
$detalles = [
    ['nombre' => 'Amisa de Trabajo GRAFA OMBU', 'tipo' => 'Amisa de Trabajo', 'marca' => 'GRAFA OMBU', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Pantalón de Trabajo GRAFA OMBU', 'tipo' => 'Pantalón de Trabajo', 'marca' => 'GRAFA OMBU', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Par de Botines de Seguridad C/P T: OMBU', 'tipo' => 'Par de Botines de Seguridad', 'marca' => 'OMBU', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Casco de Seguridad con Arnes AMARILLO LIBUS', 'tipo' => 'Casco de Seguridad', 'marca' => 'LIBUS', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Mentonera P/ CASCO LIBUS', 'tipo' => 'Mentonera P/ CASCO', 'marca' => 'LIBUS', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Chaleco Reflectivo REFLECTIVO S/M', 'tipo' => 'Chaleco Reflectivo', 'marca' => 'REFLECTIVO S/M', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Gafas de Seguridad TRANSPARENTE LIBUS', 'tipo' => 'Gafas de Seguridad', 'marca' => 'LIBUS', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Gafas de Seguridad OSCURAS LIBUS', 'tipo' => 'Gafas de Seguridad', 'marca' => 'LIBUS', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Par de Guantes VAQUETA DPS', 'tipo' => 'Par de Guantes', 'marca' => 'DPS', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    ['nombre' => 'Chaleco REFLECTIVO S/M', 'tipo' => 'Chaleco Reflectivo', 'marca' => 'S/M', 'certificacion' => 'SI', 'cantidad' => 1, 'fecha' => now()->format('Y-m-d')],
    // Agrega más elementos si es necesario
];


        // Crear una instancia de PDF
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');

        // Generar el PDF utilizando la vista personalizada y pasando los detalles y la ruta de la firma
        $pdf->loadView('ingreso', compact('persona', 'detalles', 'firmaPath'));

        // Descargar el PDF
        return $pdf->download("ingreso_de_{$persona->nombre}.pdf");
    }

    public function exportReporte($id)
    {
        // Obtener el permiso sin cargar la relación 'personal'
        $permiso = permiso::findOrFail($id);

        // Si el campo de la firma está en el mismo modelo `permiso`
        $firmaBase64 = $permiso->firma_permiso; // Asumiendo que la firma está en el campo 'firma_permiso'
        if ($firmaBase64) {
            // Decodificar la firma Base64 y guardarla como archivo temporal
            $firmaData = substr($firmaBase64, strpos($firmaBase64, ',') + 1);
            $firma = base64_decode($firmaData);
            $firmaPath = storage_path('app/temp_firma.png');
            file_put_contents($firmaPath, $firma);
        } else {
            $firmaPath = null; // En caso de que no haya firma
        }

        // Crear instancia de PDF
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');

        // Generar el PDF con la vista
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


public function CheckList($record)
{
    // Obtener la información del checklist con personal
    $checklist = checklists::with('personal')->findOrFail($record);
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');
    $pdf->loadView('checklist', compact('checklist'));

    // Descargar el PDF
    return $pdf->download('checklist.pdf');
}


public function exportPdf($record)
{
    // Obtener la persona y sus movimientos de stock
    $persona = personal::with('stockMovement')->findOrFail($record);

    // Obtener los datos de la entidad
    $entidad = entidad::firstOrFail(); // Asegúrate de que exista una fila con los datos de la entidad.

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
    $pdf->loadView('persona', compact('persona', 'firmaPath', 'entidad'));

    // Descargar el PDF
    return $pdf->download("persona_{$persona->id}.pdf");
}


public function exportCapacitacion($record)
{
    // Obtener la capacitación y los personales asociados
    $capacitacion = capacitaciones::with('personal')->findOrFail($record);

    // Crear una instancia de PDF
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');

    // Generar el PDF utilizando la vista personalizada y pasando los datos necesarios
    $pdf->loadView('certificado', compact('capacitacion'));

    // Descargar el PDF
    return $pdf->download("certificado_capacitacion_{$capacitacion->id}.pdf");
}

public function generarReporteVariacionStock($id)
{
    // Obtener el stock y su historial asociado
    $stock = Stock::with('stockHistory')->findOrFail($id);

    // Crear una instancia de PDF
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');

    // Generar el PDF utilizando la vista personalizada y pasando los datos necesarios
    $pdf->loadView('pdf.stock_variacion', compact('stock'));

    // Descargar el PDF
    return $pdf->download("variacion_stock_{$stock->id}.pdf");
}

}
