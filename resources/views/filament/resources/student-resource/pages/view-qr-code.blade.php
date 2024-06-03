<x-filament::page>
    @php
        $qrData = "Fecha de Vencimiento: " . $record->fecha_vencimiento . "\n";
        $qrData .= "Capacidad: " . $record->capacidad . "\n";
        $qrData .= "Ubicacion: " . $record->ubicacion;
    @endphp

    {!! QrCode::size(200)->generate($qrData) !!}
</x-filament::page>
