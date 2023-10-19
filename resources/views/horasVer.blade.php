<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Asistencia</title>
</head>
<body>
    <h1>Reporte de Asistencia</h1>

    <table border="1" style="width:100%">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Código</th>
                <th>Presente</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asistencia as $item)
                <tr>
                    <td>{{ $item->fecha }}</td>
                    <td>{{ $item->hora }}</td>
                    <td>{{ $item->codigo }}</td>
                    <td>{{ $item->presente ? 'Sí' : 'No' }}</td>
                    <td>{{ $item->tipo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
