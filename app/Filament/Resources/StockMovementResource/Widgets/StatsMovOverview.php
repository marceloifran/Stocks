<?php

namespace App\Filament\Resources\StockMovementResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;

use Carbon\Carbon;
use App\Models\StockMovement;

class StatsMovOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailyCount = StockMovement::whereDate('created_at', $today)->count();
        $monthlyCount = StockMovement::whereDate('created_at', '>=', $startOfMonth)->count();

        return [
            Card::make('Total De movimientos del dia', $dailyCount)
                ->icon('heroicon-o-inbox')
                ->description('Total de Stock Registrado')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
            Card::make('Total De movimientos del mes', $monthlyCount)
                ->icon('heroicon-o-inbox')
                ->description('Total de Stock Registrado')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
        ];
    }
}
