<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asistencias de {{ $persona->nombre }}</title>

    <style>
        /* Puedes agregar estilos CSS personalizados aquí */
        .imagen-personal {
            width: 80px;
            height: auto;
            justify-content: center;
            text-align: center;
            align-items: center;
        }
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
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
    </style>
</head>
<body>
    <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" class="imagen-personal" >
    <h1>Asistencias de {{ $persona->nombre }}</h1>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asistenciaCombinada as $asistencia)
                <tr>
                    <td>{{ $asistencia['fecha'] }}</td>
                    <td>{{ $asistencia['hora'] }}</td>
                    <td>{{ $asistencia['estado'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Total de asistencias: {{ $totalAsistencias }}</p>

    <h2>Total de Horas Trabajadas y Horas Extras</h2>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total Horas Trabajadas</th>
                <th>Total Horas Extras</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($horasTrabajadasPorMes as $mes => $totalHoras)
                <tr>
                    <td>{{ $mes }}</td>
                    <td>{{ $totalHoras }}</td>
                    <td>{{ $horasExtrasPorMes[$mes] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


</body>
</html>
