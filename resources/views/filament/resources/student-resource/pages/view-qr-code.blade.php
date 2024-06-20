<x-filament::page>
    @php
        $qrData = "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
        $qrData .= "Fecha de Fabricacion: " . $record->fecha_fabricacion . "\n";
        $qrData .= "Fecha de ultima Recarga: " . $record->fecha_ultima_recarga . "\n";
        $qrData .= "Capacidad: " . $record->capacidad . "\n";
        $qrData .= "Nro de Serie: " . $record->numero_serie . "\n";
        $qrData .= "Ubicacion: " . $record->ubicacion . "\n";
        $qrData .= "Responsable: " . $record->responsable_mantenimiento;

        $qrCode = app('App\Filament\Resources\MatafuegosResource\Pages\ViewQrCode')->generateQrCode($record);
    @endphp

    <div style="text-align: center;">
        <h1>Información del Matafuego</h1>
        <div style="display: flex; justify-content: center;">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
        </div>
        <p>Escanea el código QR para más información</p>
                <a href="{{ route('download.qr-code', ['id' => $record->id]) }}" class="btn btn-primary">Descargar QR Code</a> 
                <br>
                <a href="{{ route('show.qr-code', ['id' => $record->id]) }}" class="btn btn-primary">Ver QR Code</a>


    </div>
</x-filament::page>
