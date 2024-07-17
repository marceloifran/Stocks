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
            width: 100px;
            height: auto;
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
        .checkmark {
            font-size: 1.2em;
            line-height: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="ruta/a/tu/logo.png" alt="Logo de la Empresa" class="logo">
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
                        <th>Seleccionado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checklist->opciones as $opcion => $valor)
                        <tr>
                            <td>{{ $valor }}</td>
                            <td>{{ $valor == 'Sí' ? '✓' : 'X' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
