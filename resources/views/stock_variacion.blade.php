<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos de Stock</title>
    <style>
        /* Estilos aquí */
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('/images/logofinal.jpg'))) }}" alt="logo" class="h-5" style="border-radius: 5px; width:60px;">
        <div>
            <h4>Reporte de Movimientos de Stock</h4>
            <p>Fecha: {{ $fechaActual }}</p>
        </div>
    </div>

    <div class="content">
        <h3>Detalles del Stock</h3>
        <p>Obra que más gastó: {{ $obraMasGasto->obra_nombre ?? 'N/A' }} ({{ $obraMasGasto->total_gastado ?? 0 }})</p>
        <p>Persona que más gastó: {{ $personaMasGasto->personal_nombre ?? 'N/A' }} ({{ $personaMasGasto->total_gastado ?? 0 }})</p>

        <h3>Movimientos</h3>
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
                @foreach($data as $movimiento)
                    <tr>
                        <td>{{ $movimiento->id }}</td>
                        <td>{{ $movimiento->personal->obra->nombre ?? 'N/A' }}</td>
                        <td>{{ $movimiento->personal->nombre ?? 'N/A' }}</td>
                        <td>{{ $movimiento->cantidad }}</td>
                        <td>{{ $movimiento->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
