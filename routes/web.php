<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonalExportController;
use App\Http\Controllers\QRCodeController;

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

Route::get('/personal/{record}/pdf', [PersonalExportController::class, 'exportPdf'])->name('personal.exportPdf');
Route::get('/personal/pdf', [PersonalExportController::class, 'pdfpersonal'])->name('pdf.personal');
Route::get('/personal/{record}/asistencia', [PersonalExportController::class, 'porcentajeasis'])->name('personal.asistencias');
Route::get('/generar-qrs', [QRCodeController::class,'generateBulkQRs'])->name('qrcode.generateBulkQRs');

Route::get('/tomar-asistencia', [QRCodeController::class, 'iniciarAsistencia'])->name('asistencia.iniciar');
Route::get('/tomar-horas', [QRCodeController::class, 'iniciarhoras'])->name('horas.iniciar');
Route::get('asistencia-dia', [QRCodeController::class, 'dia'])->name('asistencia.dia');
Route::get('horas-dia', [QRCodeController::class, 'horas'])->name('horas.dia');
Route::get('asistencia-personal/{record}', [QRCodeController::class, 'personal'])->name('asistencia.personal');
Route::get('/asistencia-semana', [QRCodeController::class, 'semana'])->name('asistencia.semana');
Route::get('/asistencia-mes', [QRCodeController::class, 'mes'])->name('asistencia.mes');

Route::get('/horas-trabajadas-por-mes/{personalId}', [QRCodeController::class, 'horasTrabajadasPorMes'])->name('horas-trabajadas-por-mes');

Route::post('/buscar-coincidencias', [QRCodeController::class, 'buscar']);
Route::post('/buscar-coincidencias-horas', [QRCodeController::class, 'buscarHoras']);



Route::post('/guardar-asis', [QRCodeController::class, 'guardarAsistencia']);
Route::get('/asistencia-ver', [QRCodeController::class, 'asistencia'])->name('asistencia.show');


Route::post('/guardar-horas', [QRCodeController::class, 'guardarHoras']);
Route::get('/horas-ver', [QRCodeController::class, 'asistencia'])->name('asistencia.show');

Route::get('/export-porcentaje-pdf/{record}', [PersonalExportController::class,'exportPorcentajePdf'])->name('export.porcentaje.pdf');



Route::post('/verificar-registros',  [QRCodeController::class,'verificarRegistros']);


