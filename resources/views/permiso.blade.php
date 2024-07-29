<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Permiso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1, h2 {
            color: #333;
        }
        p {
            margin: 10px 0;
        }
        hr {
            border: 0;
            height: 1px;
            background: #ccc;
            margin: 20px 0;
        }
        .signature {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Reporte de Permiso</h1>

    <div>
        <h2>Información del Permiso:</h2>
        <p><strong>Tipo:</strong> {{ $permiso->tipo }}</p>
        <p><strong>Fecha:</strong> {{ $permiso->fecha }}</p>
    </div>

    <div>
        <h2>Información Personal:</h2>
        @foreach ($permiso->personal as $persona)
            <p><strong>Nombre:</strong> {{ $persona->nombre }}</p>
            <div class="signature">
                <p><strong>Firma:</strong></p>
                <img src="{{ $persona->firma }}" alt="Firma" width="200">
            </div>
            <hr>
        @endforeach
    </div>
</body>
</html>
