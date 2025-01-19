<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos de Stock</title>
    <style>
        /* Aquí puedes agregar algunos estilos si es necesario */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
    </style>
</head>
<body>

    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('/images/logofinal.jpg'))) }}" alt="logo" class="h-5" style="border-radius: 5px; width:60px;">
        <h2>Reporte de Movimientos de Stock</h2>
        <p>Fecha: {{ $fechaActual }}</p>
    </div>

    <div class="section">
        <h3>Detalles del Stock</h3>
        <p><strong>Stock:</strong> {{ $stock->nombre }}</p>
        <p><strong>Unidad:</strong> {{ $stock->unidad_medida }}</p>
        <p><strong>Tipo de Stock:</strong> {{ $stock->tipo_stock }}</p>
        <p><strong>Precio Unitario:</strong> {{ $stock->precio }}</p>

        <p><strong>Obra que más gastó:</strong> {{ $obraNombre }} ({{ $totalGastado }})</p>
        <p><strong>Persona que más gastó:</strong> {{ $personaNombre }} ({{ $totalGastadoPersona }})</p>
    </div>

    <div class="section">
        <h3>Movimientos de Stock</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Obra</th>
                    <th>Persona</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $movimiento)
                    <tr>
                        <td>{{ $movimiento->id }}</td>
                        <td>{{ $movimiento->personal->obra->nombre ?? 'N/A' }}</td>
                        <td>{{ $movimiento->personal->nombre }}</td>
                        <td>{{ $movimiento->cantidad_movimiento }}</td>
                        <td>{{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="section">
        <h2>Historial de Precios y cantidad del Stock</h2>
        @if($historialPrecios->isEmpty())
            <p>No se encontró historial de precios para este stock.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historialPrecios as $historial)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($historial->fecha_nueva)->format('d/m/Y') }}</td>
                            <td>{{ $historial->valor_anterior }}</td>
                            <td>{{ $historial->valor_nuevo }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
    </div>

</body>
</html>
