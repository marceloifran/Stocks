<?php

namespace App\Http\Controllers;

use App\Models\capacitaciones;
use App\Models\checklists;
use App\Models\Empresa;
use App\Models\entidad;
use App\Models\ingresos;
use App\Models\permiso;
use App\Models\personal;
use App\Models\stock;
use App\Models\StockHistory;
use App\Models\StockMovement;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PersonalController extends Controller
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
    $entidad = Empresa::firstOrFail(); // Asegúrate de que exista una fila con los datos de la entidad.

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

public function generatePdfByStock($record)
{
    try {
        // Obtener los movimientos de stock
        $movimientos = StockMovement::where('stock_id', $record)->get();

        // Obtener los datos de la entidad
        $entidad = Empresa::firstOrFail();

        // Calcular la obra que más gastó
        $obraMasGasto = \DB::table('stock_movements')
        ->join('personals', 'stock_movements.personal_id', '=', 'personals.id')
        ->join('obras', 'personals.obra_id', '=', 'obras.id') // Cambiar 'personal' por 'personals'
        ->select('obras.nombre as obra_nombre', \DB::raw('SUM(stock_movements.cantidad_movimiento) as total_gastado'))
        ->where('stock_movements.stock_id', $record)
        ->groupBy('obras.id','obras.nombre')
        ->orderByDesc('total_gastado')
        ->first();
    

        // Calcular la persona que más gastó
        $personaMasGasto = \DB::table('stock_movements')
            ->join('personals', 'stock_movements.personal_id', '=', 'personal.id')
            ->select('personals.nombre as personal_nombre', \DB::raw('SUM(stock_movements.cantidad_movimiento) as total_gastado'))
            ->where('stock_movements.stock_id', $record)
            ->groupBy('personal.id')
            ->orderByDesc('total_gastado')
            ->first();

        // Obtener todos los movimientos agrupados por stock
        $data = StockMovement::with('stock', 'personal.obra')
            ->where('stock_id', $record)
            ->get();

        // Crear una instancia de PDF
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('landscape');

        // Datos adicionales para la vista
        $fechaActual = now()->format('d/m/Y');

        // Generar el PDF utilizando la vista personalizada
        $pdf->loadView('stock_variacion', compact(
            'data', 'movimientos', 'entidad', 'obraMasGasto', 'personaMasGasto', 'fechaActual'
        ));

        // Descargar el PDF
        return $pdf->download("movimientos_stock_{$record}.pdf");
    } catch (\Exception $e) {
        // Registrar el error en los logs de Laravel
        \Illuminate\Support\Facades\Log::error($e->getMessage());
        return response()->json(['error' => 'Ocurrió un error al generar el PDF'], 500);
    }
}


}
