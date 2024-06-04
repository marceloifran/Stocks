<x-filament::page>
    @php
        $qrData = "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
        $qrData .= "Fecha de Fabricacion: " . $record->fecha_fabricacion . "\n";
        $qrData .= "Fecha de ultima Recarga: " . $record->fecha_ultima_recarga . "\n";
        $qrData .= "Capacidad: " . $record->capacidad . "\n";
        $qrData .= "Nro de Serie: " . $record->numero_serie . "\n";
        $qrData .= "Ubicacion: " . $record->ubicacion . "\n";
        $qrData .= "Responsable: " . $record->responsable_mantenimiento;
    @endphp
    
    <div style="text-align: center;">
        <h1>Información del Producto</h1>
        {{-- <br> --}}
        <div style="display: flex; justify-content: center;">
            {!! QrCode::size(200)->generate($qrData) !!}
        </div>
        <p>Escanea el código QR para más información</p>
    </div>
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(256)->generate('https://google.com')) !!} ">

</x-filament::page>
