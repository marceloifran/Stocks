<?php

namespace App\Http\Controllers;

use App\Models\personal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CredencialController extends Controller
{
    public function generarCredencialPDF($id)
    {
        $personal = personal::findOrFail($id);

        // Generar el código QR con el nro_identificacion como valor
        $qrValue = $personal->nro_identificacion;

        // Generar QR como SVG (no requiere imagick)
        $qrSvg = QrCode::size(200)->format('svg')->generate($qrValue);

        // Convertir SVG a base64 para incluirlo en el PDF
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        // Generar el PDF
        $pdf = PDF::loadView('credencial-pdf', [
            'personal' => $personal,
            'qrBase64' => $qrBase64
        ]);

        return $pdf->download("credencial_{$personal->nro_identificacion}.pdf");
    }

    public function verCredencial($id)
    {
        $personal = personal::findOrFail($id);

        return view('credencial-vista', [
            'personal' => $personal
        ]);
    }
}
