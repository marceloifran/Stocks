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

    /**
     * Obtener estadísticas vacías para cuando no hay datos o el usuario no tiene acceso
     */
    protected function getEmptyStats(): array
    {
        return [
            Stat::make('Emisiones Totales', '0.00 kgCO2e')
                ->description('Sin datos de emisiones')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->chart([0, 0, 0])
                ->color('gray'),

            Stat::make('Combustible', '0.00 kgCO2e')
                ->description('0% del total')
                ->descriptionIcon('heroicon-m-truck')
                ->color('gray'),

            Stat::make('Electricidad', '0.00 kgCO2e')
                ->description('0% del total')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('gray'),

            Stat::make('Residuos', '0.00 kgCO2e')
                ->description('0% del total')
                ->descriptionIcon('heroicon-m-trash')
                ->color('gray'),

            Stat::make('Tendencia', 'Estable')
                ->description('0% último mes')
                ->descriptionIcon('heroicon-m-minus')
                ->color('gray'),
        ];
    }

    protected function getStats(): array
    {
        // Filtrar por tenant_id si el usuario tiene uno
        $tenantId = auth()->user()->tenant_id;
        $query = HuellaCarbono::query();
        $detallesQuery = HuellaCarbonoDetalle::with('huellaCarbono');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
            $huellaIds = HuellaCarbono::where('tenant_id', $tenantId)->pluck('id');
            $detallesQuery->whereIn('huella_carbono_id', $huellaIds);
        } elseif (!auth()->user()->hasRole('superadmin')) {
            // Si no es superadmin y no tiene tenant, no mostrar datos
            return $this->getEmptyStats();
        }

        // Obtener emisiones totales
        $totalEmisiones = $query->sum('total_emisiones');

        // Obtener emisiones por categoría
        $detalles = $detallesQuery->get();

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

        // Calcular tendencia con filtro de tenant
        $emisionesUltimoMesQuery = HuellaCarbono::where('fecha', '>=', Carbon::now()->subDays(30));
        $emisionesAnteriorQuery = HuellaCarbono::where('fecha', '<', Carbon::now()->subDays(30))
            ->where('fecha', '>=', Carbon::now()->subDays(60));

        if ($tenantId) {
            $emisionesUltimoMesQuery->where('tenant_id', $tenantId);
            $emisionesAnteriorQuery->where('tenant_id', $tenantId);
        }

        $emisionesUltimoMes = $emisionesUltimoMesQuery->sum('total_emisiones');
        $emisionesAnterior = $emisionesAnteriorQuery->sum('total_emisiones');

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
