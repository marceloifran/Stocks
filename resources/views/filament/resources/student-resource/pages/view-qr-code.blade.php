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
        <h1>Información del Matafuego</h1>
        <img src="{{asset('/images/logo.jpeg')}}"  alt="logo" class="h-10 text-center" style="border-radius: 10px">

        {{-- <br> --}}
        <div style="display: flex; justify-content: center;">
            {!! QrCode::size(200)->generate($qrData) !!}
        </div>
        <p>Escanea el código QR para más información</p>
    </div>
    
</x-filament::page>
