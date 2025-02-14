<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\personal;
use App\Models\asistencia;
use App\Models\StockMovement;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalCount extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Movimientos del día
        $movementsToday = StockMovement::whereDate('fecha_movimiento', $today)->count();

        // Movimientos de la semana
        $movementsWeek = StockMovement::whereBetween('fecha_movimiento', [$startOfWeek, Carbon::now()])->count();

        // Movimientos del mes
        $movementsMonth = StockMovement::whereBetween('fecha_movimiento', [$startOfMonth, Carbon::now()])->count();

        // Obtener la persona con más movimientos de stock
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
        $totalStockValue = Stock::all()->sum(fn($stock) => $stock->cantidad * $stock->precio);

        // Calcular cambios porcentuales
        $previousWeekMovements = StockMovement::whereBetween('fecha_movimiento', [$startOfWeek->subWeek(), $startOfWeek])->count();
        $weekChange = $previousWeekMovements > 0 ? (($movementsWeek - $previousWeekMovements) / $previousWeekMovements) * 100 : 0;

        return [
            Stat::make('Movimientos del día', $movementsToday)
                ->description('Total de movimientos del día')
                ->icon('heroicon-o-calendar')
                ->color($movementsToday > 10 ? 'danger' : 'success'),

            Stat::make('Movimientos de la semana', $movementsWeek)
                ->description('Total de movimientos de la semana')
                ->icon('heroicon-o-calendar')
                ->color($weekChange > 0 ? 'success' : 'danger')
                ->extraAttributes(['data-change' => $weekChange]),

            Stat::make('Movimientos del mes', $movementsMonth)
                ->description('Total de movimientos del mes')
                ->icon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Stock con más movimientos', $stockWithMostMovements ? $stockWithMostMovements->nombre : 'N/A')
                ->description($stockWithMostMovements ? 'Total de movimientos: ' . $stockWithMostMovements->stock_movement_count : 'No disponible')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Stock con menos movimientos', $stockWithLeastMovements ? $stockWithLeastMovements->nombre : 'N/A')
                ->description($stockWithLeastMovements ? 'Total de movimientos: ' . $stockWithLeastMovements->stock_movement_count : 'No disponible')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('warning'),

            Stat::make('Persona con más movimientos', $personaConMasMovimientos ? $personaConMasMovimientos->nombre : 'N/A')
                ->description($personaConMasMovimientos ? 'Total de movimientos: ' . $personaConMasMovimientos->stock_movement_count : 'No disponible')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Valor Total de Stocks', '$' . number_format($totalStockValue, 2))
                ->description('Sumatoria del valor total de todos los stocks')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
