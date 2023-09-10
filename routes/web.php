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
Route::get('/generar-qrs', [QRCodeController::class,'generateBulkQRs'])->name('qrcode.generateBulkQRs');

Route::get('/tomar-asistencia', [QRCodeController::class, 'iniciarAsistencia'])->name('asistencia.iniciar');
// routes/web.php

Route::post('/buscar-coincidencias', [QRCodeController::class, 'buscar']);


Route::post('/tomar-asistencia', [QRCodeController::class, 'tomarAsistencia'])->name('asistencia.crearasistencia');

Route::post('/guardar-asistencia', [QRCodeController::class, 'guardarAsistencia']);
