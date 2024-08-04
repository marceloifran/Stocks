<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Variación de Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #ddd;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            margin: 10px 0 0 0;
        }
        .content {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            padding: 10px 0;
            text-align: center;
            border-top: 2px solid #ddd;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://via.placeholder.com/100" alt="Company Logo" class="logo">
            <h1>Variación de Stock</h1>
            <p>Producto: {{ $stock->nombre }}</p>
            <p>Reporte generado el {{ now()->format('d/m/Y') }}</p>
        </div>
        
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Fecha de Cambio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stock->stockHistory as $history)
                        <tr>
                            <td>{{ $history->valor_anterior }}</td>
                            <td>{{ $history->valor_nuevo }}</td>
                            <td>{{ \Carbon\Carbon::parse($history->fecha_nueva)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
