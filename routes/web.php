<?php

use App\Models\ingresos;
use App\Models\matafuegos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\QRCodeController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PersonalController;
use App\Filament\Resources\MatafuegosResource\Pages\ViewQrCode;
use App\Http\Controllers\ChatbotController;

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

Route::get('/personal/{record}/pdf', [PersonalController::class, 'exportPdf'])->name('personal.exportPdf');
Route::get('/personal/{record}/capacitacion', [PersonalController::class, 'exportCapacitacion'])->name('personal.capacitacion');
Route::get('/personal/{record}/ingreso', [PersonalController::class, 'exportIngreso'])->name('personal.exportIngreso');
Route::get('/personal/{record}/checklist', [PersonalController::class, 'CheckList'])->name('personal.checklist');
Route::get('/personal/reporte/{id}', [PersonalController::class, 'exportReporte'])->name('personal.exportReporte');
Route::get('/personal/pdf', [PersonalController::class, 'pdfpersonal'])->name('pdf.personal');
Route::get('/personal/{record}/asistencia', [PersonalController::class, 'porcentajeasis'])->name('personal.asistencias');
Route::get('/pdf/{record}/stock', [PersonalController::class, 'generatePdfByStock'])->name('pdf.byStock');

Route::get('/pdf/{record}/obra', [PersonalController::class, 'generatePdfByObra'])->name('pdf.byobra');













