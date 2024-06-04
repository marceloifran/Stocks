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
    {!! QrCode::size(200)->generate($qrData) !!}
    
</x-filament::page>


