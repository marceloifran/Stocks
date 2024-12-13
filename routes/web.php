<?php

use App\Models\ingresos;
use App\Models\matafuegos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\QRCodeController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PersonalExportController;
use App\Filament\Resources\MatafuegosResource\Pages\ViewQrCode;

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
Route::get('/generar-qrs', [QRCodeController::class,'generateBulkQRs'])->name('qrcode.generateBulkQRs');

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

Route::get('/firmar/{token}', function ($token) {
   $ingreso = ingresos::where('signature_token', $token)->firstOrFail();

   return view('firma', ['ingreso' => $ingreso]);
})->name('firmar');


Route::post('/firmar/{token}', function (Request $request, $token) {
   // Encuentra el ingreso asociado al token
   $ingreso = Ingresos::where('signature_token', $token)->firstOrFail();

   // Obtén la firma enviada en el formulario
   $firma = $request->input('firma');
   if (!$firma) {
       abort(400, 'No se recibió ninguna firma.');
   }

   // Decodifica y guarda la firma como imagen
   $firmaData = explode(',', $firma)[1]; // Quita el encabezado "data:image/png;base64,"
   $firmaPath = 'firmas/' . $ingreso->id . '.png';
   Storage::put($firmaPath, base64_decode($firmaData));

   // Actualiza el registro con la firma y desactiva el token
   $ingreso->update([
       'firma' => $firmaPath,
       'signature_token' => null, // El token no es válido después de firmar
   ]);

   return redirect()->route('filament.resources.ingresos.index'); // Ruta de éxito
})->name('guardar-firma');










