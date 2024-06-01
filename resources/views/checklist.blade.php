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
            <ul>
                @foreach($checklist->personal as $persona)
                    <li>{{ $persona->nombre }}</li>
                @endforeach
            </ul>
        </div>
        <div class="section">
            <strong>Opciones:</strong>
            <ul>
                @foreach($checklist->opciones as $key => $value)
                    <li>{{ $value }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</body>
</html>
