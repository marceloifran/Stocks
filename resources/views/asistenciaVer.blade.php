<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistencia Diaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 20px;
            line-height: 1.6;
        }

        h1, h2 {
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 30px;
        }

        /* Estilos para las tablas */
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #333;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }

        th {
            background-color: #f1f3f5;
            font-weight: 500;
            text-align: center;
        }

        td {
            text-align: center;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        /* Estilos para los divs de totales */
        .total-div {
            padding: 20px;
            margin: 10px;
            border-radius: 10px;
            color: white;
            text-align: center;
            font-weight: 500;
            flex: 1;
        }

        .total-div.red {
            background-color: #dc3545;
        }

        .total-div.green {
            background-color: #28a745;
        }

        .totals-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }
    </style>
  </head>
  <body>
  
    <div>
        <h1>Lista de Asistencia del Día</h1>
    </div>

    <div class="totals-container">
        <div class="total-div red">
            Total de Ausentes: {{ $totalAusentes }}
        </div>
        <div class="total-div green">
            Total de Presentes: {{ $totalPresentes }}
        </div>
    </div>

    <h2>Asistencia de Empleados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Persona</th>
                <th>Fecha Entrada</th>
                <th>Hora Entrada</th>
                <th>Fecha Salida</th>
                <th>Hora Salida</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asistenciaCombinada as $registro)
                <tr>
                    <td>{{ $registro['empleado']->nro_identificacion }}</td>
                    <td>{{ $registro['empleado']->nombre }}</td>
                    <td>{{ $registro['entrada'] ? $registro['entrada']->fecha : '' }}</td>
                    <td>{{ $registro['entrada'] ? $registro['entrada']->hora : '' }}</td>
                    <td>{{ $registro['salida'] ? $registro['salida']->fecha : '' }}</td>
                    <td>{{ $registro['salida'] ? $registro['salida']->hora : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </body>
</html>
