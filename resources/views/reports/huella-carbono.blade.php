<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Huella de Carbono - {{ $tituloPeriodo }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
        }

        .page {
            position: relative;
            width: 100%;
        }

        /* Portada */
        .cover {
            height: 1000px;
            background-color: #1e3a8a;
            color: white;
            padding: 40px;
            text-align: center;
        }

        .cover-content {
            margin-top: 150px;
            padding: 20px;
        }

        .cover h1 {
            font-size: 36px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cover h2 {
            font-size: 24px;
            font-weight: normal;
            margin-bottom: 40px;
        }

        .cover .period {
            font-size: 20px;
            padding: 10px 20px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 40px;
        }

        .cover .date {
            margin-top: 150px;
            text-align: center;
            font-size: 14px;
        }

        .cover-image {
            margin: 40px auto;
        }

        /* Contenido */
        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            color: #1e3a8a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3b82f6;
        }

        /* Resumen */
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card-total {
            background-color: #dbeafe;
            border-left: 5px solid #3b82f6;
        }

        .card-combustible {
            background-color: #fef3c7;
            border-left: 5px solid #f59e0b;
        }

        .card-electricidad {
            background-color: #fee2e2;
            border-left: 5px solid #ef4444;
        }

        .card-residuos {
            background-color: #f3f4f6;
            border-left: 5px solid #6b7280;
        }

        .card-title {
            font-size: 16px;
            margin-bottom: 10px;
            color: #4b5563;
        }

        .card-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-total .card-value {
            color: #1e40af;
        }

        .card-combustible .card-value {
            color: #b45309;
        }

        .card-electricidad .card-value {
            color: #b91c1c;
        }

        .card-residuos .card-value {
            color: #4b5563;
        }

        .card-percent {
            font-size: 14px;
            color: #6b7280;
        }

        /* Gráfico de distribución */
        .chart-container {
            margin: 30px 0;
            text-align: center;
        }

        .pie-chart-container {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            position: relative;
        }

        .pie-chart-slice {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
        }

        .slice-combustible {
            background-color: #f59e0b;
            clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 0, {{ 50 - 50 * cos((3.6 * $porcentajes['combustible'] * pi()) / 180) }}% {{ 50 - 50 * sin((3.6 * $porcentajes['combustible'] * pi()) / 180) }}%);
            z-index: 3;
        }

        .slice-electricidad {
            background-color: #ef4444;
            clip-path: polygon(50% 50%, {{ 50 - 50 * cos((3.6 * $porcentajes['combustible'] * pi()) / 180) }}% {{ 50 - 50 * sin((3.6 * $porcentajes['combustible'] * pi()) / 180) }}%, {{ 50 - 50 * cos((3.6 * ($porcentajes['combustible'] + $porcentajes['electricidad']) * pi()) / 180) }}% {{ 50 - 50 * sin((3.6 * ($porcentajes['combustible'] + $porcentajes['electricidad']) * pi()) / 180) }}%);
            z-index: 2;
        }

        .slice-residuos {
            background-color: #6b7280;
            clip-path: polygon(50% 50%, {{ 50 - 50 * cos((3.6 * ($porcentajes['combustible'] + $porcentajes['electricidad']) * pi()) / 180) }}% {{ 50 - 50 * sin((3.6 * ($porcentajes['combustible'] + $porcentajes['electricidad']) * pi()) / 180) }}%, 0 100%, 0 0);
            z-index: 1;
        }

        .pie-chart-legend {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 8px;
            border-radius: 3px;
        }

        .legend-combustible {
            background-color: #f59e0b;
        }

        .legend-electricidad {
            background-color: #ef4444;
        }

        .legend-residuos {
            background-color: #6b7280;
        }

        /* Tabla de registros */
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }

        .records-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .records-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .records-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .records-table tr:hover {
            background-color: #f3f4f6;
        }

        /* Tipo de fuente */
        .tag {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tag-combustible {
            background-color: #fef3c7;
            color: #b45309;
        }

        .tag-electricidad {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .tag-residuos {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        /* Paginación y pie de página */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #1e3a8a;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 12px;
        }

        .page-number {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 12px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }

        /* Barras de progreso */
        .progress-container {
            width: 100%;
            height: 15px;
            background-color: #e5e7eb;
            border-radius: 10px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
        }

        .progress-combustible {
            background-color: #f59e0b;
            width: {{ $porcentajes['combustible'] }}%;
        }

        .progress-electricidad {
            background-color: #ef4444;
            width: {{ $porcentajes['electricidad'] }}%;
        }

        .progress-residuos {
            background-color: #6b7280;
            width: {{ $porcentajes['residuos'] }}%;
        }
    </style>
</head>

<body>
    <!-- Portada -->
    <div class="cover">
        <div class="cover-content">
            <h1>Informe de Huella de Carbono</h1>
            <h2>Análisis de Emisiones de CO2</h2>
            <div class="period">{{ $tituloPeriodo }}</div>
            <div class="cover-image">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none"
                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M16.24 7.76a6 6 0 0 1-8.49 8.48" />
                    <path d="M14.12 10.88a3 3 0 0 1-4.24 4.24" />
                    <path d="M8.53 16.11a6 6 0 0 1-.4-8.15" />
                    <path d="M9.88 14.12a3 3 0 0 1-.4-4.24" />
                </svg>
            </div>
        </div>
        <div class="date">Fecha de generación: {{ $fechaGeneracion }}</div>
    </div>

    <div class="page-break"></div>

    <!-- Contenido - Resumen -->
    <div class="content">
        <div class="section">
            <h2 class="section-title">Resumen Ejecutivo</h2>

            <p>Este informe presenta un análisis detallado de la huella de carbono generada durante el periodo
                seleccionado. Las emisiones se clasifican en tres categorías principales: combustibles, electricidad y
                residuos.</p>

            <div class="summary-grid">
                <div class="summary-card card-total">
                    <div class="card-title">EMISIONES TOTALES</div>
                    <div class="card-value">{{ number_format($totalEmisiones, 2) }}</div>
                    <div class="card-percent">kilogramos de CO2e</div>
                </div>

                <div class="summary-card card-combustible">
                    <div class="card-title">COMBUSTIBLES</div>
                    <div class="card-value">{{ number_format($estadisticas['combustible'], 2) }}</div>
                    <div class="card-percent">{{ $porcentajes['combustible'] }}% del total</div>
                    <div class="progress-container">
                        <div class="progress-bar progress-combustible"></div>
                    </div>
                </div>

                <div class="summary-card card-electricidad">
                    <div class="card-title">ELECTRICIDAD</div>
                    <div class="card-value">{{ number_format($estadisticas['electricidad'], 2) }}</div>
                    <div class="card-percent">{{ $porcentajes['electricidad'] }}% del total</div>
                    <div class="progress-container">
                        <div class="progress-bar progress-electricidad"></div>
                    </div>
                </div>

                <div class="summary-card card-residuos">
                    <div class="card-title">RESIDUOS</div>
                    <div class="card-value">{{ number_format($estadisticas['residuos'], 2) }}</div>
                    <div class="card-percent">{{ $porcentajes['residuos'] }}% del total</div>
                    <div class="progress-container">
                        <div class="progress-bar progress-residuos"></div>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h3>Distribución de Emisiones por Categoría</h3>

                <!-- Gráfico de distribución simplificado -->
                <table style="width: 80%; margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        <td
                            style="width: {{ $porcentajes['combustible'] }}%; background-color: #f59e0b; height: 30px;">
                        </td>
                        <td
                            style="width: {{ $porcentajes['electricidad'] }}%; background-color: #ef4444; height: 30px;">
                        </td>
                        <td style="width: {{ $porcentajes['residuos'] }}%; background-color: #6b7280; height: 30px;">
                        </td>
                    </tr>
                </table>

                <div class="pie-chart-legend">
                    <div class="legend-item">
                        <div class="legend-color legend-combustible"></div>
                        <div>Combustibles ({{ $porcentajes['combustible'] }}%)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-electricidad"></div>
                        <div>Electricidad ({{ $porcentajes['electricidad'] }}%)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-residuos"></div>
                        <div>Residuos ({{ $porcentajes['residuos'] }}%)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Contenido - Detalle de Registros -->
    <div class="content">
        <div class="section">
            <h2 class="section-title">Detalle de Emisiones</h2>

            <p>A continuación se presenta el detalle de todas las emisiones registradas durante el periodo
                {{ $tituloPeriodo }}. Los datos están ordenados cronológicamente, mostrando las fuentes específicas de
                emisiones.</p>

            <table class="records-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Categoría</th>
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
                                        $categoria = '';
                                        $tagClass = '';

                                        if (isset($detalle->detalles['categoria'])) {
                                            $categoria = $detalle->detalles['categoria'];

                                            switch ($categoria) {
                                                case 'combustible':
                                                    $tagClass = 'tag-combustible';
                                                    $categoria = 'Combustible';
                                                    break;
                                                case 'electricidad':
                                                    $tagClass = 'tag-electricidad';
                                                    $categoria = 'Electricidad';
                                                    break;
                                                case 'residuos':
                                                    $tagClass = 'tag-residuos';
                                                    $categoria = 'Residuos';
                                                    break;
                                            }
                                        } else {
                                            $categoria = $detalle->tipo_fuente;
                                            $tagClass = 'tag-combustible';
                                        }
                                    @endphp
                                    <span class="tag {{ $tagClass }}">{{ $categoria }}</span>
                                </td>
                                <td>{{ $detalle->detalles['identificador_fuente'] ?? '-' }}</td>
                                <td>{{ number_format($detalle->cantidad, 2) }} {{ $detalle->unidad }}</td>
                                <td><strong>{{ number_format($detalle->emisiones_co2, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Conclusiones y Recomendaciones</h2>

            <p>En base al análisis de los datos recopilados durante el periodo {{ $tituloPeriodo }}, se pueden extraer
                las siguientes conclusiones:</p>

            <ul>
                <li>Las emisiones totales ascienden a {{ number_format($totalEmisiones, 2) }} kgCO2e.</li>
                <li>La principal fuente de emisiones es {{ array_keys($estadisticas, max($estadisticas))[0] }},
                    representando el {{ $porcentajes[array_keys($estadisticas, max($estadisticas))[0]] }}% del total.
                </li>
            </ul>

            <p><strong>Recomendaciones para reducir la huella de carbono:</strong></p>

            <ul>
                <li>Optimizar el consumo de combustibles mediante el mantenimiento adecuado de vehículos y maquinaria.
                </li>
                <li>Implementar medidas de eficiencia energética para reducir el consumo eléctrico.</li>
                <li>Mejorar la gestión de residuos, promoviendo la reducción, reutilización y reciclaje.</li>
                <li>Considerar la compensación de emisiones mediante proyectos de reforestación o energías renovables.
                </li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Informe de Huella de Carbono | © {{ date('Y') }} Comprehensive Management System
    </div>
</body>

</html>
