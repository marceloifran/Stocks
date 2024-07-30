<!DOCTYPE html>
<html>
<head>
    <title>Comprobante de Sueldo</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        .details th, .details td { padding: 10px; text-align: left; }
        .details { border-collapse: collapse; width: 100%; }
        .details th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Comprobante de Sueldo</h1>
            <p>ID: {{ $sueldo->id }}</p>
            <p>Mes: {{ $sueldo->mes }}</p>
            <p>Año: {{ $sueldo->anio }}</p>
        </div>
        <table class="details">
            <tr>
                <th>Horas Normales</th>
                <td>{{ $sueldo->horas_normales }}</td>
            </tr>
            <tr>
                <th>Horas Extras</th>
                <td>{{ $sueldo->horas_extras }}</td>
            </tr>
            <tr>
                <th>Pago por Horas Normales</th>
                <td>{{ $sueldo->pago_horas_normales }}</td>
            </tr>
            <tr>
                <th>Pago por Horas Extras</th>
                <td>{{ $sueldo->pago_horas_extras }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>{{ $sueldo->total }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
