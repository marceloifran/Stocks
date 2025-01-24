<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Obra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            border-radius: 5px;
        }
        h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('/images/logofinal.jpg'))) }}" alt="logo">
        <h2>Reporte de Obra</h2>
        <p><strong>Entidad:</strong> {{ $entidad->nombre }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h3>Detalles de la Obra</h3>
        <p><strong>Nombre:</strong> {{ $obra->nombre }}</p>
        <p><strong>Estado:</strong> {{ $obra->estado }}</p>
        <p><strong>Fecha de arranque:</strong> {{ $obra->fecha_arranque }}</p>
        <p><strong>Fecha de finalización:</strong> {{ $obra->fecha_final }}</p>
    </div>

    <div class="section">
        <h3>Personal Asignado</h3>
        @if($obra->personal->isEmpty())
            <p>No hay personal asignado a esta obra.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Puesto</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($obra->personal as $persona)
                        <tr>
                            <td>{{ $persona->nombre }}</td>
                            <td>{{ $persona->dni }}</td>
                            <td>{{ $persona->puesto }}</td>
                            <td>{{ $persona->telefono }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h3>Movimientos de Stock</h3>
        @if($obra->personal->isEmpty())
            <p>No se encontraron movimientos de stock relacionados con esta obra.</p>
        @else
            @foreach ($obra->personal as $persona)
                @if($persona->stockMovement->isNotEmpty())
                    <h4>Movimientos de Stock para {{ $persona->nombre }}</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Stock</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($persona->stockMovement as $movimiento)
                                <tr>
                                    <td>{{ $movimiento->stock->nombre }}</td>
                                    <td>{{ $movimiento->cantidad_movimiento }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y') }}</td>
                                    <td>{{ $movimiento->responsable }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
        @endif
    </div>

</body>
</html>
