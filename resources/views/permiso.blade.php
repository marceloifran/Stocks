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
        .signature img {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h1>Reporte de Permiso</h1>

    <div>
        <h2>Información del Permiso:</h2>
        <p><strong>Tipo de Trabajo:</strong> {{ implode(', ', $permiso->tipo_trabajo) }}</p>
        <p><strong>Fecha de Inicio:</strong> {{ $permiso->fecha_inicio }}</p>
        <p><strong>Fecha de Fin:</strong> {{ $permiso->fecha_fin }}</p>
        <p><strong>Trabajadores Capacitados:</strong> {{ $permiso->capacitados ? 'Sí' : 'No' }}</p>
        <p><strong>Equipos a Intervenir:</strong> {{ $permiso->equipos_a_intervenir }}</p>
        <p><strong>Elementos:</strong> {{ implode(', ', $permiso->elementos ?? []) }}</p>
    </div>

    <div class="signature">
        <h2>Firma:</h2>
        @if ($firmaPath)
            <img src="{{ $firmaPath }}" alt="Firma" width="200">
        @else
            <p>No hay firma disponible</p>
        @endif
    </div>

    <div>
        <h2>Cierre del Permiso:</h2>
        <p><strong>Cierre:</strong> {{ implode(', ', $permiso->cierre ?? []) }}</p>
        <p><strong>Fecha de Cierre:</strong> {{ $permiso->fecha_fin_pte }}</p>
    </div>
</body>
</html>
