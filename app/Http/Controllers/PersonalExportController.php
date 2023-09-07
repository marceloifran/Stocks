<?php

namespace App\Http\Controllers;

use App\Models\personal;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class PersonalExportController extends Controller
{
    public function exportPdf($record)
    {
        // Obtener la persona y sus movimientos de stock
        $persona = personal::with('stockMovement')->findOrFail($record);



        // Crear una instancia de PDF
        $pdf = app('dompdf.wrapper');

        $pdf->setPaper('landscape');


        // Generar el PDF utilizando la vista personalizada
        $pdf->loadView('persona', compact('persona'));

        // Descargar el PDF
        return $pdf->download("persona_{$persona->id}.pdf");
    }
}
