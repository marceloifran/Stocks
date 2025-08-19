<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Huella de Carbono - {{ $tituloPeriodo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        .period-selector {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
        }

        .summary {
            margin-bottom: 30px;
        }

        .summary h2 {
            color: #1f2937;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-box {
            width: 30%;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-box.combustible {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
        }

        .stat-box.electricidad {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
        }

        .stat-box.residuos {
            background-color: #f3f4f6;
            border-left: 4px solid #6b7280;
        }

        .stat-box h3 {
            margin: 0 0 10px;
            font-size: 16px;
        }

        .stat-box p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .stat-box .percent {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .total-emissions {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background-color: #dbeafe;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .total-emissions h3 {
            margin: 0 0 10px;
            color: #1e40af;
        }

        .total-emissions p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }

        .records {
            margin-top: 30px;
        }

        .records h2 {
            color: #1f2937;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Huella de Carbono</h1>
        <p>Fecha de generación: {{ $fechaGeneracion }}</p>
    </div>

    <div class="period-selector">
        Periodo: {{ $tituloPeriodo }}
    </div>

    <div class="summary">
        <h2>Resumen de Emisiones</h2>

        <div class="total-emissions">
            <h3>Emisiones Totales</h3>
            <p>{{ number_format($totalEmisiones, 2) }} kgCO2e</p>
        </div>

        <div class="stats">
            <div class="stat-box combustible">
                <h3>Combustibles</h3>
                <p>{{ number_format($estadisticas['combustible'], 2) }} kgCO2e</p>
                <div class="percent">{{ $porcentajes['combustible'] }}% del total</div>
            </div>

            <div class="stat-box electricidad">
                <h3>Electricidad</h3>
                <p>{{ number_format($estadisticas['electricidad'], 2) }} kgCO2e</p>
                <div class="percent">{{ $porcentajes['electricidad'] }}% del total</div>
            </div>

            <div class="stat-box residuos">
                <h3>Residuos</h3>
                <p>{{ number_format($estadisticas['residuos'], 2) }} kgCO2e</p>
                <div class="percent">{{ $porcentajes['residuos'] }}% del total</div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="records">
        <h2>Registros del Periodo</h2>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Identificador</th>
                    <th>Cantidad</th>
                    <th>Emisiones (kgCO2e)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $registro)
                    @foreach ($registro->detalles as $detalle)
                        <tr>
                            <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $tipoFuente = '';
                                    if (isset($detalle->detalles['categoria'])) {
                                        switch ($detalle->detalles['categoria']) {
                                            case 'combustible':
                                                $tipoFuente = 'Combustible';
                                                break;
                                            case 'electricidad':
                                                $tipoFuente = 'Electricidad';
                                                break;
                                            case 'residuos':
                                                $tipoFuente = 'Residuos';
                                                break;
                                            default:
                                                $tipoFuente = $detalle->tipo_fuente;
                                        }
                                    } else {
                                        $tipoFuente = $detalle->tipo_fuente;
                                    }
                                @endphp
                                {{ $tipoFuente }}
                            </td>
                            <td>{{ $detalle->detalles['identificador_fuente'] ?? '-' }}</td>
                            <td>{{ number_format($detalle->cantidad, 2) }} {{ $detalle->unidad }}</td>
                            <td>{{ number_format($detalle->emisiones_co2, 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema de gestión de huella de carbono.</p>
        <p>&copy; {{ date('Y') }} Comprehensive Management System</p>
    </div>
</body>

</html>
