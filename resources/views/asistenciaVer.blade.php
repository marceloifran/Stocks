<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistencia Diaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
        .imagen-personal {
            width: 80px;
            height: auto;
            margin: 0 auto; /* Centrar la imagen horizontalmente */
            display: block;
        }
        body {
            font-family: 'Poppins', sans-serif; /* Fuente Poppins */
        }

        /* Estilos para las tablas */
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #333; /* Cambiar el color del texto de la tabla si es necesario */
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6; /* Cambiar los bordes de la tabla si es necesario */
        }

        th {
            background-color: #f8f9fa; /* Cambiar el fondo de las cabeceras de tabla si es necesario */
            text-align: left;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2; /* Cambiar el fondo de las filas impares de la tabla si es necesario */
        }

        /* Estilos para los div de totales */
        .total-div {
            padding: 20px;
            display: inline-block;
            margin: 0 10px;
            border-radius: 15px;
            color: white;
        }

        .total-div.red {
            background-color: red;
        }

        .total-div.green {
            background-color: green;
        }
    </style>

  </head>
  <body>
    {{-- <div style="display: flex; align-items: center; flex-direction: column; text-align: center; margin-bottom: 70px;">
        <h1 style="margin-bottom: 10px;">Lista de Asistencia del Día</h1>
        <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" class="imagen-personal" style="margin-bottom: 40px;">
    </div> --}}

    <div style="display: flex; align-items: center; flex-direction: column; text-align: center; margin-bottom: 70px;">
        <h1 style="margin-bottom: 10px;">Lista de Asistencia del Día</h1>
        <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" class="imagen-personal">
    </div>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center;">
        <div class="total-div red">
            Total de Ausentes: {{ $totalAusentes }}
        </div>
        <div class="total-div green">
            Total de Presentes: {{ $totalPresentes }}
        </div>
    </div>

<!-- ... tu código HTML anterior ... -->

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

<!-- ... tu código HTML anterior ... -->


</html>
