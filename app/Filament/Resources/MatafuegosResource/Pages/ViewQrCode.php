<?php
namespace App\Filament\Resources\MatafuegosResource\Pages;

use App\Filament\Resources\MatafuegosResource;
use App\Models\matafuegos;
use Filament\Pages\Actions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Resources\Pages\ViewRecord;

class ViewQrCode extends ViewRecord
{
    protected static string $resource = MatafuegosResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.view-qr-code';

    public function generateQrCode($record)
    {
        $qrData = "";

        // $qrData = "ID: " . $record->id . "\n";
        $qrData = "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
        $qrData .= "Fecha de Fabricacion: " . $record->fecha_fabricacion . "\n";
        $qrData .= "Fecha de ultima Recarga: " . $record->fecha_ultima_recarga . "\n";
        $qrData .= "Capacidad: " . $record->capacidad . "\n";
        $qrData .= "Nro de Serie: " . $record->numero_serie . "\n";
        $qrData .= "Ubicacion: " . $record->ubicacion . "\n";
        $qrData .= "Responsable: " . $record->responsable_mantenimiento;

        $qrCode = QrCode::format('png')
            ->size(200)
            ->generate($qrData);

        return base64_encode($qrCode);
    }

    public function downloadQrCode($id)
    {
        $record = matafuegos::findOrFail($id); // Usa el modelo directamente para obtener el registro

        $qrData = "";
        $qrData .= "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
        $qrData .= "Fecha de Fabricacion: " . $record->fecha_fabricacion . "\n";
        $qrData .= "Fecha de ultima Recarga: " . $record->fecha_ultima_recarga . "\n";
        $qrData .= "Capacidad: " . $record->capacidad . "\n";
        $qrData .= "Nro de Serie: " . $record->numero_serie . "\n";
        $qrData .= "Ubicacion: " . $record->ubicacion . "\n";
        $qrData .= "Responsable: " . $record->responsable_mantenimiento;

        $qrCode = QrCode::format('png')
            ->size(200)
            ->generate($qrData);

        $fileName = 'qr_code_' . $id . '.png';
        $filePath = storage_path('app/public/' . $fileName);

        file_put_contents($filePath, $qrCode);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    public function showQrCode($id)
    {
        $record = matafuegos::findOrFail($id);
        $qrCode = $this->generateQrCode($record);
        return view('filament.resources.student-resource.pages.show-qr-code', compact('record', 'qrCode'));
    }



    protected function getHeaderActions(): array
    {
        return [];
    }
}
