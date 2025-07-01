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
        }

        .header {
            background: linear-gradient(to right, #1e40af, #3b82f6);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .content {
            padding: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .detail {
            width: 48%;
        }

        .detail span {
            display: block;
            font-size: 12px;
            color: #666;
        }

        .detail p {
            margin: 0;
            font-weight: bold;
            font-size: 14px;
        }

        .qr-code {
            text-align: center;
            margin-bottom: 15px;
        }

        .footer {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="credencial">
        <div class="header">
            <h1>Credencial de Empleado</h1>
        </div>

        <div class="content">
            <div class="info">
                <h2>{{ $personal->nombre }}</h2>
                <p>{{ $personal->departamento ?? 'Sin departamento asignado' }}</p>
            </div>

            <div class="details">
                <div class="detail">
                    <span>ID</span>
                    <p>{{ $personal->nro_identificacion }}</p>
                </div>

                <div class="detail">
                    <span>DNI</span>
                    <p>{{ $personal->dni ?? 'No registrado' }}</p>
                </div>
            </div>

            <div class="qr-code">
                <img src="{{ $qrBase64 }}" style="width: 150px;">
                <p style="margin-top: 5px; font-size: 12px; color: #666;">ID: {{ $personal->nro_identificacion }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Esta credencial es personal e intransferible</p>
            <p>Fecha de emisión: {{ date('d/m/Y') }}</p>
        </div>
    </div>
</body>

</html>
