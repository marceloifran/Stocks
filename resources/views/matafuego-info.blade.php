<!DOCTYPE html>
<html>
<head>
    <title>Información del Matafuego</title>
</head>
<body>
    <div style="text-align: center;">
        <h1>Información del Matafuego</h1>

        {{-- Mostrar la información del matafuego --}}
        <p><strong>Fecha de Vencimiento:</strong> {{ $matafuego->fecha_vencimiento }}</p>
        <p><strong>Fecha de Fabricación:</strong> {{ $matafuego->fecha_fabricacion }}</p>
        <p><strong>Fecha de Última Recarga:</strong> {{ $matafuego->fecha_ultima_recarga }}</p>
        <p><strong>Capacidad:</strong> {{ $matafuego->capacidad }}</p>
        <p><strong>Número de Serie:</strong> {{ $matafuego->numero_serie }}</p>
        <p><strong>Ubicación:</strong> {{ $matafuego->ubicacion }}</p>
        <p><strong>Responsable de Mantenimiento:</strong> {{ $matafuego->responsable_mantenimiento }}</p>

        {{-- Generar QR para la URL --}}
        @php
            $url = route('matafuego.qr', ['id' => $matafuego->id]);
        @endphp

        <div style="display: flex; justify-content: center;">
            {!! QrCode::size(200)->generate($url) !!}
        </div>
        <p>Escanea el código QR para más información</p>
    </div>
</body>
</html>
