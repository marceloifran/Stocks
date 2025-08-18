<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HuellaCarbonoWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;

        // Total de emisiones del mes actual
        $emisionesMes = HuellaCarbono::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->sum('total_emisiones');

        // Emisiones por tipo de fuente en el mes actual
        $emisionesCombustible = HuellaCarbonoDetalle::whereHas('huellaCarbono', function ($query) use ($mesActual, $anioActual) {
            $query->whereMonth('fecha', $mesActual)
                ->whereYear('fecha', $anioActual);
        })
            ->whereHas('huellaCarbono')
            ->where(function ($query) {
                $query->whereJsonContains('detalles->categoria', 'combustible')
                    ->orWhere('tipo_fuente', 'like', '%gasolina%')
                    ->orWhere('tipo_fuente', 'like', '%diesel%')
                    ->orWhere('tipo_fuente', 'like', '%gnc%');
            })
            ->sum('emisiones_co2');

        $emisionesElectricidad = HuellaCarbonoDetalle::whereHas('huellaCarbono', function ($query) use ($mesActual, $anioActual) {
            $query->whereMonth('fecha', $mesActual)
                ->whereYear('fecha', $anioActual);
        })
            ->whereHas('huellaCarbono')
            ->where(function ($query) {
                $query->whereJsonContains('detalles->categoria', 'electricidad')
                    ->orWhere('tipo_fuente', 'like', '%electricidad%');
            })
            ->sum('emisiones_co2');

        $emisionesResiduos = HuellaCarbonoDetalle::whereHas('huellaCarbono', function ($query) use ($mesActual, $anioActual) {
            $query->whereMonth('fecha', $mesActual)
                ->whereYear('fecha', $anioActual);
        })
            ->whereHas('huellaCarbono')
            ->where(function ($query) {
                $query->whereJsonContains('detalles->categoria', 'residuos')
                    ->orWhere('tipo_fuente', 'like', '%residuo%');
            })
            ->sum('emisiones_co2');

        // Calcular porcentajes
        $totalEmisiones = $emisionesCombustible + $emisionesElectricidad + $emisionesResiduos;

        $porcentajeCombustible = $totalEmisiones > 0 ? round(($emisionesCombustible / $totalEmisiones) * 100, 1) : 0;
        $porcentajeElectricidad = $totalEmisiones > 0 ? round(($emisionesElectricidad / $totalEmisiones) * 100, 1) : 0;
        $porcentajeResiduos = $totalEmisiones > 0 ? round(($emisionesResiduos / $totalEmisiones) * 100, 1) : 0;

        // Emisiones totales históricas
        $emisionesTotal = HuellaCarbono::sum('total_emisiones');

        return [
            Stat::make('Huella de Carbono (Mes)', number_format($emisionesMes, 2) . ' kgCO2e')
                ->description('Total emisiones del mes')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Combustibles', number_format($emisionesCombustible, 2) . ' kgCO2e')
                ->description($porcentajeCombustible . '% del total')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Electricidad', number_format($emisionesElectricidad, 2) . ' kgCO2e')
                ->description($porcentajeElectricidad . '% del total')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('danger'),

            Stat::make('Residuos', number_format($emisionesResiduos, 2) . ' kgCO2e')
                ->description($porcentajeResiduos . '% del total')
                ->descriptionIcon('heroicon-m-trash')
                ->color('gray'),

            Stat::make('Huella de Carbono (Total)', number_format($emisionesTotal, 2) . ' kgCO2e')
                ->description('Emisiones históricas totales')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}
