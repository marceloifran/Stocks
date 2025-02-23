<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Personal;
use App\Models\Asistencia;
use App\Models\StockMovement;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class PersonalCount extends BaseWidget
{
    protected static bool $isLazy = true;
    protected int $pollInterval = 60;

    protected function getStats(): array
    {
        return cache()->remember('dashboard.personal-stats', 300, function () {
            $today = Carbon::today();
            $startOfWeek = Carbon::now()->startOfWeek();
            $startOfMonth = Carbon::now()->startOfMonth();

            // Movimientos del día
            $movementsToday = StockMovement::whereDate('fecha_movimiento', Carbon::today())
                ->whereYear('fecha_movimiento', now()->year)
                ->count();

            // Movimientos de la semana
            $movementsWeek = StockMovement::whereBetween('fecha_movimiento', [
                Carbon::now()->startOfWeek()->startOfDay(),
                Carbon::now()->endOfWeek()->endOfDay()
            ])->count();

            // Movimientos del mes
            $movementsMonth = StockMovement::whereBetween('fecha_movimiento', [$startOfMonth, Carbon::now()])->count();

            // Persona con más movimientos
            $personaConMasMovimientos = Personal::withCount('stockMovement')
                ->orderBy('stock_movement_count', 'desc')
                ->first();

            // Stock con más movimientos
            $stockWithMostMovements = Stock::withCount('stockMovement')
                ->orderByDesc('stock_movement_count')
                ->first();

            // Stock con menos movimientos
            $stockWithLeastMovements = Stock::withCount('stockMovement')
                ->orderBy('stock_movement_count')
                ->first();

            // Sumatoria del valor total de los stocks
            $totalStockValue = Stock::sum(DB::raw('cantidad * precio'));

            // Calcular cambios porcentuales de la semana
            $previousWeekStart = Carbon::now()->subWeek()->startOfWeek()->startOfDay();
            $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek()->endOfDay();
            $previousWeekMovements = StockMovement::whereBetween('fecha_movimiento',
                [$previousWeekStart, $previousWeekEnd])->count();

            $weekChange = $previousWeekMovements > 0
                ? (($movementsWeek - $previousWeekMovements) / $previousWeekMovements) * 100
                : 0;

            // Stocks en nivel crítico (asumiendo que existe la columna stock_minimo)
            $stocksEnNivelCritico = Stock::where('cantidad', '<=', DB::raw('stock_minimo'))->count();

            // Datos para el gráfico de tendencias
            $weeklyTrends = $this->getWeeklyTrends();

            return [
                Stat::make('Movimientos del día', $movementsToday)
                    ->description('Total de movimientos del día')
                    ->icon('heroicon-o-calendar')
                    ->color($movementsToday > 10 ? 'danger' : 'success'),

                Stat::make('Movimientos de la semana', $movementsWeek)
                    ->description(sprintf('%.1f%% respecto a la semana anterior', $weekChange))
                    ->icon('heroicon-o-calendar')
                    ->color($weekChange > 0 ? 'success' : 'danger'),

                Stat::make('Movimientos del mes', $movementsMonth)
                    ->description('Total de movimientos del mes')
                    ->icon('heroicon-o-calendar')
                    ->color('info'),

                Stat::make('Stock con más movimientos', $stockWithMostMovements?->nombre ?? 'N/A')
                    ->description($stockWithMostMovements
                        ? "Total: {$stockWithMostMovements->stock_movement_count} movimientos"
                        : 'Sin datos')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success'),

                Stat::make('Stock con menos movimientos', $stockWithLeastMovements?->nombre ?? 'N/A')
                    ->description($stockWithLeastMovements
                        ? "Total: {$stockWithLeastMovements->stock_movement_count} movimientos"
                        : 'Sin datos')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->color('warning'),

                Stat::make('Persona con más movimientos', $personaConMasMovimientos?->nombre ?? 'N/A')
                    ->description($personaConMasMovimientos
                        ? "Total: {$personaConMasMovimientos->stock_movement_count} movimientos"
                        : 'Sin datos')
                    ->icon('heroicon-o-user-group')
                    ->color('primary'),

                Stat::make('Valor Total de Stocks', '$' . number_format($totalStockValue, 2))
                    ->description('Valor total del inventario')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success'),

                Stat::make('Stocks en nivel crítico', $stocksEnNivelCritico)
                    ->description('Productos bajo nivel mínimo')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),

                Stat::make('Tendencia semanal', sprintf('%.1f%%', $weekChange))
                    ->chart($weeklyTrends)
                    ->color($weekChange > 0 ? 'success' : 'danger'),
            ];
        });
    }

    protected function getWeeklyTrends(): array
    {
        $trends = [];
        for ($i = 4; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $movements = StockMovement::whereBetween('fecha_movimiento', [$weekStart, $weekEnd])
                ->count();

            $trends[] = $movements;
        }

        return $trends;
    }
}
