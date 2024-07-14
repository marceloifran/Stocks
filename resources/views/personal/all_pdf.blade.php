<!DOCTYPE html>
<html>
<head>
    <title>QR Personal PDF</title>
    <style>
        @page {
            size: A4; /* Tamaño de página A4 */
            margin: 0; /* Sin márgenes para maximizar el espacio */
        }
        body {
            font-family: Arial, sans-serif; /* Fuente para el cuerpo del documento */
            margin: 20px; /* Márgenes para el cuerpo del documento */
        }
        .container {
            display: inline-block; /* Mostrar como bloques en línea */
            width: 45%; /* Ancho del contenedor */
            margin: 10px; /* Márgenes entre las tarjetas */
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            width: 80px; /* Tamaño del logo */
            border-radius: 25%; /* Bordes redondeados para el logo */
            margin-top: 10px;
        }
        .personal-info {
            margin-top: 10px;
            font-size: 16px;
        }
        .qr-code {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    @foreach($personals as $personal)
        <div class="container">
            <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            <div class="personal-info">
                <h3>{{ $personal->nombre }}</h3>
            </div>
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(100)->generate($personal->nro_identificacion)) }}" alt="QR Code">
            </div>
        </div>
    @endforeach
</body>
</html>
