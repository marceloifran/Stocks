<?php

use App\Models\matafuegos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ComidaController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PersonalExportController;
use App\Filament\Resources\MatafuegosResource\Pages\ViewQrCode;
use App\Http\Controllers\CredencialController;
use App\Http\Controllers\HuellaCarbonoReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/download-qr-code/{id}', [ViewQrCode::class, 'downloadQrCode'])->name('download.qr-code');
Route::get('/show-qr-code/{id}', [ViewQrCode::class, 'showQrCode'])->name('show.qr-code');


Route::get('/personals/pdf', [QRCodeController::class, 'generateAllPdf'])->name('personals.pdf');
Route::get('/sueldo/{record}/pdf', [QRCodeController::class, 'generatesueldo'])->name('sueldo.comprobante');




// Route::get('/personal/{id}/qr', [QRCodeController::class, 'showQr'])->name('personal.qr');
// Route::get('/personal/{id}/pdf', [QRCodeController::class, 'generatePdf'])->name('personal.pdf');

Route::get('/personal/{record}/pdf', [PersonalExportController::class, 'exportPdf'])->name('personal.exportPdf');
Route::get('/personal/{record}/capacitacion', [PersonalExportController::class, 'exportCapacitacion'])->name('personal.capacitacion');
// Route::get('/reporte/variacion-stock', [PersonalExportController::class, 'generarReporteVariacionStock'])->name('reporte.variacion_stock');
Route::get('/reporte/variacion-stock/{id}', [PersonalExportController::class, 'generarReporteVariacionStock'])->name('reporte.variacion_stock');



Route::get('/personal/{record}/ingreso', [PersonalExportController::class, 'exportIngreso'])->name('personal.exportIngreso');
Route::get('/personal/{record}/checklist', [PersonalExportController::class, 'CheckList'])->name('personal.checklist');
Route::get('/personal/reporte/{id}', [PersonalExportController::class, 'exportReporte'])->name('personal.exportReporte');
Route::get('/personal/pdf', [PersonalExportController::class, 'pdfpersonal'])->name('pdf.personal');
Route::get('/personal/{record}/asistencia', [PersonalExportController::class, 'porcentajeasis'])->name('personal.asistencias');
Route::get('/generar-qrs', [QRCodeController::class, 'generateBulkQRs'])->name('qrcode.generateBulkQRs');

Route::get('/tomar-asistencia', [QRCodeController::class, 'iniciarAsistencia'])->name('asistencia.iniciar');
Route::get('asistencia-dia', [QRCodeController::class, 'dia'])->name('asistencia.dia');
Route::get('asistencia-personal/{record}', [QRCodeController::class, 'personal'])->name('asistencia.personal');
Route::get('/asistencia-semana', [QRCodeController::class, 'semana'])->name('asistencia.semana');
Route::get('/asistencia-mes', [QRCodeController::class, 'mes'])->name('asistencia.mes');

Route::get('/horas-trabajadas-por-mes/{record}', [QRCodeController::class, 'horasTrabajadasPorMes'])->name('horas-trabajadas-por-mes');

Route::post('/buscar-coincidencias', [QRCodeController::class, 'buscar']);
Route::post('/buscar-coincidencias-horas', [QRCodeController::class, 'buscarHoras']);



Route::post('/guardar-asis', [QRCodeController::class, 'guardarAsistencia']);
Route::get('/asistencia-ver', [QRCodeController::class, 'asistencia'])->name('asistencia.show');

// Rutas para el control de comidas
Route::get('/tomar-comida', [ComidaController::class, 'iniciarComida'])->name('comida.iniciar');
Route::post('/guardar-comida', [ComidaController::class, 'guardarComida']);
Route::get('/comida-reporte', [ComidaController::class, 'generarReporte'])->name('comida.reporte');
Route::get('/comida-personal/{record}', [ComidaController::class, 'reportePersonal'])->name('comida.personal');

// Rutas para credenciales y firmas
Route::get('/personal/credencial/{id}/pdf', [CredencialController::class, 'generarCredencialPDF'])->name('personal.credencial.pdf');
Route::get('/personal/credencial/{id}/ver', [CredencialController::class, 'verCredencial'])->name('personal.credencial.ver');

Route::get('/firma/{id}', function ($id) {
    $stockMovement = \App\Models\StockMovement::with(['personal', 'stock'])->findOrFail($id);
    return view('firma-detalle', compact('stockMovement'));
})->name('firma.ver');

// Ruta para el reporte de huella de carbono
Route::get('/huella-carbono/report', [HuellaCarbonoReportController::class, 'generateReport'])
    ->name('huella-carbono.report');
