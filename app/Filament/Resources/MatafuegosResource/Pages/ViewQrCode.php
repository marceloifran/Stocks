<?php

namespace App\Filament\Resources\MatafuegosResource\Pages;

use App\Filament\Resources\MatafuegosResource;
use Filament\Pages\Actions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Filament\Resources\Pages\ViewRecord;

class ViewQrCode extends ViewRecord
{
protected static string $resource = MatafuegosResource::class;

protected static string $view = 'filament.resources.student-resource.pages.view-qr-code';
public function generateQrCodeWithLogo($record)
{
    $qrData = "ID: " . $record->id . "\n";
    $qrData .= "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
    $qrData .= "Fecha de Fabricacion: " . $record->fecha_fabricacion . "\n";
    $qrData .= "Fecha de ultima Recarga: " . $record->fecha_ultima_recarga . "\n";
    $qrData .= "Capacidad: " . $record->capacidad . "\n";
    $qrData .= "Nro de Serie: " . $record->numero_serie . "\n";
    $qrData .= "Ubicacion: " . $record->ubicacion . "\n";
    $qrData .= "Responsable: " . $record->responsable_mantenimiento;

    $qrCode = QrCode::format('png')
        ->size(200);

    // Dimensiones del logo y posición relativa en el QR
    $logoPath = public_path('images/logo.png');
    $logoWidth = 50; // Tamaño del logo en pixels
    $qrCode = $qrCode->merge($logoPath, 0.5, true, $logoWidth);

    $qrCode = $qrCode->generate($qrData);

    return base64_encode($qrCode);
}



protected function getHeaderActions(): array
{
    return [];
}
}