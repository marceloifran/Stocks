<x-filament::page>
    <div style="text-align: center;">
        <h1>Información del Matafuego</h1>
        <div style="display: flex; justify-content: center; align-items: center;">
            <div style="margin-right: 20px;">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            </div>
            <div>
                <h2>Datos del Matafuego</h2>
                <p>Fecha de Vencimiento: {{ $record->fecha_vencimiento }}</p>
                <p>Fecha de Fabricacion: {{ $record->fecha_fabricacion }}</p>
                <p>Fecha de ultima Recarga: {{ $record->fecha_ultima_recarga }}</p>
                <p>Capacidad: {{ $record->capacidad }}</p>
                <p>Nro de Serie: {{ $record->numero_serie }}</p>
                <p>Ubicacion: {{ $record->ubicacion }}</p>
                <p>Responsable: {{ $record->responsable_mantenimiento }}</p>
            </div>
        </div>
    </div>
</x-filament::page>
