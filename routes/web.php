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
    return view('welcome');
});

Route::get('/personal/{record}/pdf', [PersonalExportController::class, 'exportPdf'])->name('personal.exportPdf');
Route::get('/personal/pdf', [PersonalExportController::class, 'pdfpersonal'])->name('pdf.personal');
Route::get('/personal/{record}/asistencia', [PersonalExportController::class, 'porcentajeasis'])->name('personal.asistencias');
Route::get('/generar-qrs', [QRCodeController::class,'generateBulkQRs'])->name('qrcode.generateBulkQRs');

Route::get('/tomar-asistencia', [QRCodeController::class, 'iniciarAsistencia'])->name('asistencia.iniciar');
Route::get('asistencia-dia', [QRCodeController::class, 'dia'])->name('asistencia.dia');
Route::get('/asistencia-semana', [QRCodeController::class, 'semana'])->name('asistencia.semana');
Route::get('/asistencia-mes', [QRCodeController::class, 'mes'])->name('asistencia.mes');

Route::post('/buscar-coincidencias', [QRCodeController::class, 'buscar']);



Route::post('/guardar-asis', [QRCodeController::class, 'guardarAsistencia']);
Route::get('/asistencia-ver', [QRCodeController::class, 'asistencia'])->name('asistencia.show');

Route::get('/export-porcentaje-pdf/{record}', [PersonalExportController::class,'exportPorcentajePdf'])->name('export.porcentaje.pdf');



