@php
    use App\Models\HuellaCarbono;
    use App\Models\HuellaCarbonoDetalle;

    // Calcular el total de emisiones
    $totalEmisiones = HuellaCarbono::sum('total_emisiones');

    // Calcular estadísticas por tipo de fuente
    $estadisticas = [
        'combustible' => 0,
        'electricidad' => 0,
        'residuos' => 0,
    ];

    // Obtener todos los detalles
    $detalles = HuellaCarbonoDetalle::with('huellaCarbono')->get();

    // Calcular totales por categoría
    foreach ($detalles as $detalle) {
        if (isset($detalle->detalles['categoria'])) {
            $categoria = $detalle->detalles['categoria'];
            if (isset($estadisticas[$categoria])) {
                $estadisticas[$categoria] += $detalle->emisiones_co2;
            }
        }
    }

    // Calcular porcentajes
    $porcentajes = [];
    foreach ($estadisticas as $tipo => $valor) {
        $porcentajes[$tipo] = $totalEmisiones > 0 ? round(($valor / $totalEmisiones) * 100, 1) : 0;
    }
@endphp

<div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col gap-2">
    <div class="flex justify-between items-center">
        <span class="font-medium text-gray-700 dark:text-gray-300">Total de emisiones:</span>
        <span class="font-bold text-primary-600 dark:text-primary-400">{{ number_format($totalEmisiones, 2) }}
            kgCO2e</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Combustible:</span>
                <span class="text-sm font-semibold">{{ number_format($estadisticas['combustible'], 2) }} kgCO2e</span>
            </div>
            <div class="mt-1 h-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" style="width: {{ $porcentajes['combustible'] }}%"></div>
            </div>
            <div class="text-xs text-right mt-1 text-gray-500 dark:text-gray-400">{{ $porcentajes['combustible'] }}%
            </div>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Electricidad:</span>
                <span class="text-sm font-semibold">{{ number_format($estadisticas['electricidad'], 2) }} kgCO2e</span>
            </div>
            <div class="mt-1 h-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $porcentajes['electricidad'] }}%"></div>
            </div>
            <div class="text-xs text-right mt-1 text-gray-500 dark:text-gray-400">{{ $porcentajes['electricidad'] }}%
            </div>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Residuos:</span>
                <span class="text-sm font-semibold">{{ number_format($estadisticas['residuos'], 2) }} kgCO2e</span>
            </div>
            <div class="mt-1 h-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width: {{ $porcentajes['residuos'] }}%"></div>
            </div>
            <div class="text-xs text-right mt-1 text-gray-500 dark:text-gray-400">{{ $porcentajes['residuos'] }}%</div>
        </div>
    </div>
</div>
