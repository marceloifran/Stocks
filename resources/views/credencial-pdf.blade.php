<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Credencial de {{ $personal->nombre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .credencial {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            background-color: #ffffff;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eaeaea;
        }

        .logo {
            max-width: 180px;
            max-height: 60px;
        }

        .content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-code {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        .info {
            width: 100%;
            margin-top: 15px;
        }

        .info-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 8px;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            display: block;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }

        .footer {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .credencial-title {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="credencial">
        <div class="header">
            <img src="{{ public_path('images/logo.jpeg') }}" alt="Logo" class="logo">
            <div class="credencial-title">CREDENCIAL</div>
        </div>

        <div class="content">
            <div class="qr-code">
                <img src="{{ $qrBase64 }}" style="width: 150px;">
            </div>

            <div class="info">
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <p class="info-value">{{ $personal->nombre }}</p>
                </div>

                <div class="info-item">
                    <span class="info-label">DNI:</span>
                    <p class="info-value">{{ $personal->dni ?? 'No registrado' }}</p>
                </div>

                <div class="info-item">
                    <span class="info-label">Departamento:</span>
                    <p class="info-value">{{ $personal->departamento ?? 'Sin departamento asignado' }}</p>
                </div>

                <div class="info-item">
                    <span class="info-label">ID:</span>
                    <p class="info-value">{{ $personal->nro_identificacion }}</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Esta credencial es personal e intransferible</p>
            <p>Fecha de emisión: {{ date('d/m/Y') }}</p>
        </div>
    </div>
</body>

</html>
