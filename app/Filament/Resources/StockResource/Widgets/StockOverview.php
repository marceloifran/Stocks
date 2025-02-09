<?php

namespace App\Filament\Resources\StockResource\Widgets;

use App\Models\personal;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockOverview extends BaseWidget
{
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
        $personaConMasMovimientos = personal::withCount('stockMovement')
        ->orderBy('stock_movement_count', 'desc')
        ->first();

        // Stock con más movimientos
        $stockWithMostMovements = Stock::withCount('stockMovement')
            ->orderByDesc('stock_movement_count')
            ->first();

        // Stock con menos movimientos
        $stockWithLeastMovements = Stock::withCount('stockMovement')
            ->orderByDesc('stock_movement_count')
            ->first();

        return [
            Stat::make('Movimientos del día', $movementsToday)
                ->description('Total de movimientos del día')
                ->icon('heroicon-o-calendar'),

            Stat::make('Movimientos de la semana', $movementsWeek)
                ->description('Total de movimientos de la semana')
                ->icon('heroicon-o-calendar'),

            Stat::make('Movimientos del mes', $movementsMonth)
                ->description('Total de movimientos del mes')
                ->icon('heroicon-o-calendar'),

            Stat::make('Stock con más movimientos', $stockWithMostMovements ? $stockWithMostMovements->nombre : 'N/A')
                ->description($stockWithMostMovements ? 'Total de movimientos: ' . $stockWithMostMovements->stock_movement_count : 'No disponible')
                ->icon('heroicon-o-arrow-trending-up'),

            Stat::make('Stock con menos movimientos', $stockWithLeastMovements ? $stockWithLeastMovements->nombre : 'N/A')
                ->description($stockWithLeastMovements ? 'Total de movimientos: ' . $stockWithLeastMovements->stock_movement_count : 'No disponible')
                ->icon('heroicon-o-arrow-trending-down'),
                Stat::make(
                    'Persona con más movimientos',
                    $personaConMasMovimientos ? $personaConMasMovimientos->nombre : 'N/A'
                )
                ->description(
                    $personaConMasMovimientos
                        ? 'Total de movimientos: ' . $personaConMasMovimientos->stock_movements_count
                        : 'No disponible'
                )
                ->icon('heroicon-o-user-group'),
        ];
    }
}
