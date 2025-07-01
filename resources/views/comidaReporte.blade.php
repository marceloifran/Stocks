<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte de Comidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
            line-height: 1.6;
        }

        h1 {
            font-family: 'Roboto', sans-serif;
            margin-bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #333;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        th {
            background-color: #f8f9fa;
            text-align: left;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .total-div {
            padding: 20px;
            margin: 10px;
            border-radius: 15px;
            color: white;
            text-align: center;
            flex: 1;
        }

        .total-div.blue {
            background-color: #007bff;
        }

        .totals-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }
    </style>
</head>

<body>
    <div>
        <h1>Reporte de Comidas</h1>
    </div>

    <div class="totals-container">
        <div class="total-div blue">
            Total de Comidas: {{ $totalComidas }}
        </div>
    </div>

    <h2>Comidas por Tipo</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tipo de Comida</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comidasPorTipo as $tipo => $cantidad)
                <tr>
                    <td>{{ ucfirst($tipo) }}</td>
                    <td>{{ $cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detalle de Comidas</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Persona</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Tipo de Comida</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comidas as $comida)
                <tr>
                    <td>{{ $comida->codigo }}</td>
                    <td>{{ $comida->personal->nombre }}</td>
                    <td>{{ $comida->fecha }}</td>
                    <td>{{ $comida->hora }}</td>
                    <td>{{ ucfirst($comida->tipo_comida) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
