<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class HuellaCarbonoResumenWidget extends BaseWidget
{
    protected static ?string $heading = 'Huella de Carbono';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Obtener emisiones totales
        $totalEmisiones = HuellaCarbono::sum('total_emisiones');

        // Obtener emisiones por categoría
        $detalles = HuellaCarbonoDetalle::with('huellaCarbono')->get();

        $emisionesPorCategoria = [
            'combustible' => 0,
            'electricidad' => 0,
            'residuos' => 0,
        ];

        foreach ($detalles as $detalle) {
            if (isset($detalle->detalles['categoria'])) {
                $categoria = $detalle->detalles['categoria'];
                if (isset($emisionesPorCategoria[$categoria])) {
                    $emisionesPorCategoria[$categoria] += $detalle->emisiones_co2;
                }
            }
        }

        // Calcular porcentajes
        $porcentajes = [];
        foreach ($emisionesPorCategoria as $tipo => $valor) {
            $porcentajes[$tipo] = $totalEmisiones > 0 ? round(($valor / $totalEmisiones) * 100, 1) : 0;
        }

        // Calcular tendencia
        $emisionesUltimoMes = HuellaCarbono::where('fecha', '>=', Carbon::now()->subDays(30))->sum('total_emisiones');
        $emisionesAnterior = HuellaCarbono::where('fecha', '<', Carbon::now()->subDays(30))
            ->where('fecha', '>=', Carbon::now()->subDays(60))
            ->sum('total_emisiones');

        $tendencia = 'estable';
        $porcentajeCambio = 0;

        if ($emisionesAnterior > 0) {
            $porcentajeCambio = round((($emisionesUltimoMes - $emisionesAnterior) / $emisionesAnterior) * 100, 1);
            if ($porcentajeCambio > 5) {
                $tendencia = 'aumento';
            } elseif ($porcentajeCambio < -5) {
                $tendencia = 'disminución';
            }
        }

        // Crear estadísticas
        return [
            Stat::make('Emisiones Totales', number_format($totalEmisiones, 2) . ' kgCO2e')
                ->description('Emisiones acumuladas')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->chart([
                    $emisionesPorCategoria['combustible'],
                    $emisionesPorCategoria['electricidad'],
                    $emisionesPorCategoria['residuos']
                ])
                ->color('primary'),

            Stat::make('Combustible', number_format($emisionesPorCategoria['combustible'], 2) . ' kgCO2e')
                ->description($porcentajes['combustible'] . '% del total')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->extraAttributes(['class' => 'combustible-card']),

            Stat::make('Electricidad', number_format($emisionesPorCategoria['electricidad'], 2) . ' kgCO2e')
                ->description($porcentajes['electricidad'] . '% del total')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('danger')
                ->extraAttributes(['class' => 'electricidad-card']),

            Stat::make('Residuos', number_format($emisionesPorCategoria['residuos'], 2) . ' kgCO2e')
                ->description($porcentajes['residuos'] . '% del total')
                ->descriptionIcon('heroicon-m-trash')
                ->color('gray')
                ->extraAttributes(['class' => 'residuos-card']),

            Stat::make('Tendencia', $tendencia == 'estable' ? 'Estable' : ($tendencia == 'aumento' ? 'En aumento' : 'En disminución'))
                ->description($porcentajeCambio > 0 ? '+' . $porcentajeCambio . '% último mes' : $porcentajeCambio . '% último mes')
                ->descriptionIcon($tendencia == 'aumento' ? 'heroicon-m-arrow-trending-up' : ($tendencia == 'disminución' ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($tendencia == 'aumento' ? 'danger' : ($tendencia == 'disminución' ? 'success' : 'gray')),
        ];
    }
}
