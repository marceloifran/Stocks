<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Personal PDF</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 45%;
            margin: 10px auto;
            padding: 15px;
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            page-break-inside: avoid;
        }

        .logo {
            width: 60px;
            margin-bottom: 10px;
        }

        .personal-info {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333333;
        }

        .qr-code {
            margin-top: 15px;
        }

        .legal-note {
            margin-top: 20px;
            font-size: 12px;
            color: #777777;
        }

        h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #444444;
        }
    </style>
</head>

<body>
    @foreach ($personals as $personal)
        <div class="container">
            <img src="{{ public_path('images/logoifsin.png') }}" class="logo" alt="Logo">
            <div class="personal-info">
                <h3>{{ $personal->nombre }}</h3>
            </div>
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(120)->generate($personal->nro_identificacion)) }}"
                    alt="QR Code">
            </div>
            <div class="legal-note">
                <p>Esta tarjeta es de carácter legal y único.</p>
            </div>
        </div>
    @endforeach
</body>

</html>
