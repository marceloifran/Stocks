<!DOCTYPE html>
<html>
<head>
    <title>QR Personal PDF</title>
    <style>
        .container {
            text-align: center;
            margin: 50px;
        }
        .logo {
            width: 150px;
        }
        .qr-code {
            margin-top: 20px;
        }
        .personal-info {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
        <div class="personal-info">
            <p><strong>Nombre:</strong> {{ $personal->nombre }}</p>
        </div>
        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
        </div>
    </div>
</body>
</html>
