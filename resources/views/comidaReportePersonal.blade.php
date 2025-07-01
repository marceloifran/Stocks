<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte de Comidas de {{ $persona->nombre }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #ddd;
        }

        .header img {
            width: 100px;
            height: auto;
        }

        .header h1 {
            margin: 10px 0 0 0;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }

        .content {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I"
                alt="Company Logo">
            <h1>Reporte de Comidas de {{ $persona->nombre }}</h1>
            <p>Reporte generado el {{ now()->format('d/m/Y') }}</p>
        </div>

        <div class="content">
            <h2>Resumen de Comidas por Tipo</h2>
            <table>
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

            <h2>Comidas por Día</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Desayuno</th>
                        <th>Almuerzo</th>
                        <th>Merienda</th>
                        <th>Cena</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comidasPorDia as $fecha => $tipos)
                        <tr>
                            <td>{{ $fecha }}</td>
                            <td>{{ $tipos['desayuno'] ?? 0 }}</td>
                            <td>{{ $tipos['almuerzo'] ?? 0 }}</td>
                            <td>{{ $tipos['merienda'] ?? 0 }}</td>
                            <td>{{ $tipos['cena'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>Detalle de Comidas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo de Comida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comidas as $comida)
                        <tr>
                            <td>{{ $comida->fecha }}</td>
                            <td>{{ $comida->hora }}</td>
                            <td>{{ ucfirst($comida->tipo_comida) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Total de comidas registradas: {{ $totalComidas }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>
