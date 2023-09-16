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
            float: right
        }
        body {
            font-family: 'Poppins', sans-serif; /* Cambiar la fuente a Poppins */
        }

        /* Estilos para las tablas */
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #333; /* Cambiar el color del texto de la tabla si es necesario */
            border-collapse: collapse;
        }

        th,
        td {
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
    </style>

  </head>
  <body>
    <div style="display: flex; align-items: center; flex-direction: column; text-align: center; margin-bottom: 70px;">
        <h1 style="margin-bottom: 10px;">Lista de Asistencia del Día</h1>
        <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="" class="imagen-personal" style="margin-bottom: 40px;">
    </div>


    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center;">
        <div style="background-color: red; padding: 20px; display: inline-block; margin-right: 10px;">
            <p style="color: white">Total de Ausentes: {{ $totalAusentes }}</p>
        </div>
        <div style="background-color: green; padding: 20px; display: inline-block; margin-left: 10px;">
            <p style="color: white">Total de Presentes: {{ $totalPresentes }}</p>
        </div>
    </div>

<h2>Empleados Presentes</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Persona</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($personal as $empleado)
            @if (in_array($empleado->nro_identificacion, $asistencia->pluck('codigo')->toArray()))
            @php
            $asistenciaEmpleado = $asistencia->where('codigo', $empleado->nro_identificacion)->first();
        @endphp
                <tr>
                    <td>{{ $empleado->nro_identificacion }}</td>
                    <td>{{ $empleado->nombre }}</td>
                    <td>{{ $asistenciaEmpleado->fecha }}</td>
                    <td>{{ $asistenciaEmpleado->hora }}</td>
                    <td>Presente</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

<h2 class="">Empleados Ausentes</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Persona</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($personal as $empleado)
            @if (!in_array($empleado->nro_identificacion, $asistencia->pluck('codigo')->toArray()))
                <tr>
                    <td>{{ $empleado->nro_identificacion }}</td>
                    <td>{{ $empleado->nombre }}</td>
                    <td>Ausente</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
  </body>

</html>
