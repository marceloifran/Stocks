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

        // Crear barras de progreso para los porcentajes
        $barrasCombustible = $this->crearBarraProgreso($porcentajes['combustible'], 'warning');
        $barrasElectricidad = $this->crearBarraProgreso($porcentajes['electricidad'], 'danger');
        $barrasResiduos = $this->crearBarraProgreso($porcentajes['residuos'], 'gray');

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
                ->description(new HtmlString($barrasCombustible . '<span class="ml-1">' . $porcentajes['combustible'] . '% del total</span>'))
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Electricidad', number_format($emisionesPorCategoria['electricidad'], 2) . ' kgCO2e')
                ->description(new HtmlString($barrasElectricidad . '<span class="ml-1">' . $porcentajes['electricidad'] . '% del total</span>'))
                ->descriptionIcon('heroicon-m-bolt')
                ->color('danger'),

            Stat::make('Residuos', number_format($emisionesPorCategoria['residuos'], 2) . ' kgCO2e')
                ->description(new HtmlString($barrasResiduos . '<span class="ml-1">' . $porcentajes['residuos'] . '% del total</span>'))
                ->descriptionIcon('heroicon-m-trash')
                ->color('gray'),

            Stat::make('Tendencia', $this->formatearTendencia($tendencia))
                ->description($this->formatearPorcentajeCambio($porcentajeCambio))
                ->descriptionIcon($tendencia == 'aumento' ? 'heroicon-m-arrow-trending-up' : ($tendencia == 'disminución' ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($tendencia == 'aumento' ? 'danger' : ($tendencia == 'disminución' ? 'success' : 'gray')),
        ];
    }

    /**
     * Crea una barra de progreso visual HTML
     */
    protected function crearBarraProgreso($porcentaje, $color): string
    {
        $barras = '';
        $totalBarras = 10;
        $barrasLlenas = round($porcentaje / 10);

        $colorClase = match ($color) {
            'warning' => 'bg-amber-500',
            'danger' => 'bg-red-500',
            'success' => 'bg-green-500',
            'gray' => 'bg-gray-500',
            default => 'bg-blue-500',
        };

        for ($i = 0; $i < $totalBarras; $i++) {
            if ($i < $barrasLlenas) {
                $barras .= "<span class='inline-block w-1.5 h-3 {$colorClase} mx-0.5 rounded-sm'></span>";
            } else {
                $barras .= "<span class='inline-block w-1.5 h-3 bg-gray-200 mx-0.5 rounded-sm'></span>";
            }
        }

        return $barras;
    }

    /**
     * Formatea la tendencia para mostrarla con un icono
     */
    protected function formatearTendencia($tendencia): string
    {
        return match ($tendencia) {
            'aumento' => 'En aumento ↑',
            'disminución' => 'En disminución ↓',
            default => 'Estable →',
        };
    }

    /**
     * Formatea el porcentaje de cambio con color
     */
    protected function formatearPorcentajeCambio($porcentajeCambio): string
    {
        $signo = $porcentajeCambio > 0 ? '+' : '';
        $clase = $porcentajeCambio > 0 ? 'text-red-500 font-medium' : ($porcentajeCambio < 0 ? 'text-green-500 font-medium' : 'text-gray-500');

        return new HtmlString("<span class='{$clase}'>{$signo}{$porcentajeCambio}% último mes</span>");
    }
}
