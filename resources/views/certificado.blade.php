<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado de Capacitación</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
        }
        .container {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
        }
        .header {
            margin-bottom: 50px;
        }
        .content {
            margin-bottom: 50px;
        }
        .footer {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Certificado de Capacitación</h1>
        </div>
        @foreach($capacitacion->personal as $personal)
        <div class="content">
            <p>Se certifica que</p>
            <h2>{{ $personal->nombre }} {{ $personal->apellido }}</h2>
            <p>ha completado la capacitación</p>
            <h3>{{ $capacitacion->tematica }}</h3>
            <p>impartida por</p>
            <h4>{{ $capacitacion->capacitador }}</h4>
            <p>en modalidad</p>
            <h4>{{ ucfirst($capacitacion->modalidad) }}</h4>
            <p>el día</p>
            <h4>{{ \Carbon\Carbon::parse($capacitacion->fecha)->format('d/m/Y') }}</h4>
        </div>
        @endforeach
        <div class="footer">
            <p>Observaciones:</p>
            <p>{{ $capacitacion->observaciones }}</p>
        </div>
    </div>
</body>
</html>
