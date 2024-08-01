<!DOCTYPE html>
<html>
<head>
    <title>QR Code</title>
</head>
<body>
    <div style="text-align: center;">
        <h1>Información del Matafuego</h1>
        <div style="display: flex; justify-content: center;">
            {!! QrCode::size(200)->generate(route('matafuego.info', ['id' => $matafuego->id])) !!}
        </div>
        <p>Escanea el código QR para más información</p>
    </div>
</body>
</html>
