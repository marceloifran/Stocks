<?php

namespace App\Http\Controllers;

use App\Models\obra;
use App\Models\stock;
use App\Models\Empresa;
use App\Models\entidad;
use App\Models\permiso;
use App\Models\ingresos;
use App\Models\personal;
use Barryvdh\DomPDF\PDF;
use App\Models\checklists;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\capacitaciones;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PersonalController extends Controller
{


    public function pdfpersonal()
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

    // Verificar si hay movimientos
    $firmaPath = null;
    if ($persona->stockMovement->isNotEmpty()) {
        // Decodificar la firma Base64 y guardarla como archivo temporal
        $firmaBase64 = $persona->stockMovement->first()->firma;
        $firmaData = substr($firmaBase64, strpos($firmaBase64, ',') + 1);
        $firma = base64_decode($firmaData);
        $firmaPath = storage_path('app/temp_firma.png');
        file_put_contents($firmaPath, $firma);
    }

    // Crear una instancia de PDF
    $pdf = app('dompdf.wrapper');
    $pdf->setPaper('landscape');

    // Generar el PDF utilizando la vista personalizada y pasando la ruta de la firma
    $pdf->loadView('persona', compact('persona', 'firmaPath', 'entidad'));

    // Descargar el PDF
    return $pdf->download("299_de_{$persona->nombre}.pdf");
}


    public function generatePdfByStock($record)
    {
        try {
            // Obtener los movimientos de stock
            $movimientos = StockMovement::where('stock_id', $record)->get();
    
            // Obtener los datos de la entidad
            $entidad = Empresa::firstOrFail();
    
            // Calcular la obra que más gastó
            $obraMasGasto = StockMovement::where('stock_id', $record)
                ->with(['personal.obra']) // Cargar la relación personal -> obra
                ->selectRaw('sum(cantidad_movimiento) as total_gastado, personal_id')
                ->groupBy('personal_id')
                ->orderByDesc('total_gastado')
                ->first();
    
            $obraNombre = $obraMasGasto ? $obraMasGasto->personal->obra->nombre : 'N/A';
            $totalGastado = $obraMasGasto ? $obraMasGasto->total_gastado : 0;
    
            // Calcular la persona que más gastó
            $personaMasGasto = StockMovement::where('stock_id', $record)
                ->with(['personal.obra']) // Cargar la relación personal -> obra
                ->selectRaw('sum(cantidad_movimiento) as total_gastado, personal_id')
                ->groupBy('personal_id')
                ->orderByDesc('total_gastado')
                ->first();
    
            $personaNombre = $personaMasGasto ? $personaMasGasto->personal->nombre : 'N/A';
            $totalGastadoPersona = $personaMasGasto ? $personaMasGasto->total_gastado : 0;
    
            // Obtener todos los movimientos agrupados por stock
            $data = StockMovement::with('stock', 'personal.obra')
                ->where('stock_id', $record)
                ->get();
    
            // Obtener el historial de precios del stock
            $stock = Stock::findOrFail($record);
            $historialPrecios = $stock->stockhistory()->orderBy('fecha_nueva')->get();
    
            // Crear una instancia de PDF
            $pdf = app('dompdf.wrapper');
            $pdf->setPaper('landscape');
    
            // Datos adicionales para la vista
            $fechaActual = now()->format('d/m/Y');
    
            // Generar el PDF utilizando la vista personalizada
            $pdf->loadView('stock_variacion', compact(
                'data', 'movimientos', 'entidad', 'obraNombre', 'totalGastado', 'stock','personaNombre', 'totalGastadoPersona', 'fechaActual', 'historialPrecios'
            ));
    
            // Descargar el PDF
            return $pdf->download("movimientos_stock_{$stock->nombre}.pdf");
        } catch (\Exception $e) {
            // Registrar el error en los logs de Laravel
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al generar el PDF'], 500);
        }
    }

    //genera un reporte de obra con la informacion de la obra, el personal que trabajo 
    //en ella y los materiales que se utilizaron
    public function generatePdfByObra($record)
    {
        try {
            // Obtener la obra con el personal relacionado
            $obra = Obra::with('personal.stockMovement.stock')->findOrFail($record);

            // Obtener los datos de la entidad
            $entidad = Empresa::firstOrFail();

            // Crear una instancia de PDF
            $pdf = app('dompdf.wrapper');
            $pdf->setPaper('landscape');

            // Generar el PDF utilizando la vista personalizada
            $pdf->loadView('obra', compact('obra', 'entidad'));

            // Descargar el PDF
            return $pdf->download("reporte_obra_{$obra->nombre}.pdf");
        } catch (\Exception $e) {
            // Registrar el error en los logs de Laravel
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al generar el PDF'], 500);
        }
    }
    
    
}
