<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asistencias de {{ $persona->nombre }}</title>

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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="Company Logo">
            <h1>Asistencias de {{ $persona->nombre }}</h1>
            <p>Reporte generado el {{ now()->format('d/m/Y') }}</p>
        </div>

        <div class="content">
            <h2>Horas por Día</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total Horas Normales</th>
                        <th>Total Horas Extras</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asistenciasPorDia as $fecha => $asistencia)
                        <tr>
                            <td>{{ $fecha }}</td>
                            <td>{{ number_format($asistencia['horas_normales'], 2) }}</td>
                            <td>{{ number_format($asistencia['horas_extras'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>Horas por Semana</h2>
            <table>
                <thead>
                    <tr>
                        <th>Semana</th>
                        <th>Total Horas Normales</th>
                        <th>Total Horas Extras</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asistenciasPorSemana as $semana => $asistencia)
                        <tr>
                            <td>Semana {{ $semana }}</td>
                            <td>{{ number_format($asistencia['horas_normales'], 2) }}</td>
                            <td>{{ number_format($asistencia['horas_extras'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>Horas por Quincena</h2>
            <table>
                <thead>
                    <tr>
                        <th>Quincena</th>
                        <th>Total Horas Normales</th>
                        <th>Total Horas Extras</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asistenciasPorQuincena as $quincena => $asistencia)
                        <tr>
                            <td>{{ $quincena }}</td>
                            <td>{{ number_format($asistencia['horas_normales'], 2) }}</td>
                            <td>{{ number_format($asistencia['horas_extras'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Total de asistencias: {{ $totalAsistencias }}</p>
            <p>Total de horas normales: {{ number_format($totalHorasNormales, 2) }}</p>
            <p>Total de horas extras: {{ number_format($totalHorasExtras, 2) }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
