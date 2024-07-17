<!DOCTYPE html>
<html>
<head>
    <title>CheckList PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            margin: 0 30px;
        }
        .section {
            margin-bottom: 15px;
        }
        .logo {
            width: 50px;
            height: 50px;
            display: block;
            margin: 0 auto 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .options-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://media.licdn.com/dms/image/C4E0BAQGhkLET1-UZPQ/company-logo_200_200/0/1641320084310?e=2147483647&v=beta&t=Oknns7rgyanOzrEi0fSiusmVYEAt3DdLZ5fxbNRzk0I" alt="Company Logo" class="logo">
        <h2>CheckList</h2>
    </div>
    <div class="content">
        <div class="section">
            <strong>Autorización:</strong> {{ $checklist->autorizacion }}
        </div>
        <div class="section">
            <strong>Fecha:</strong> {{ $checklist->fecha }}
        </div>
        <div class="section">
            <strong>Personal Involucrado:</strong>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checklist->personal as $persona)
                        <tr>
                            <td>{{ $persona->nombre }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="section">
            <strong>Opciones:</strong>
            <table class="options-table">
                <thead>
                    <tr>
                        <th>Opción</th>
                        <th>Sí</th>
                        <th>No</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $opciones = json_decode($checklist->opciones, true);
                    @endphp
                    @foreach($opciones as $opcion)
                        <tr>
                            <td>{{ $opcion['nombre'] }}</td>
                            <td>{{ $opcion['valor'] == 'Sí' ? 'x' : '' }}</td>
                            <td>{{ $opcion['valor'] == 'No' ? 'x' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
