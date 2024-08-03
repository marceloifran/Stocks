<?php

namespace App\Filament\Resources\MatafuegosResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Filament\Resources\MatafuegosResource;

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
    
        $qrCode = $qrCode;
    
        $qrCode = $qrCode->generate($qrData);
    
        return base64_encode($qrCode);
    }
}