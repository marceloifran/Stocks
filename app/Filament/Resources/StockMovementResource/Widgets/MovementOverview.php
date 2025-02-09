<?php

namespace App\Filament\Resources\StockMovementResource\Widgets;

use App\Models\StockMovement;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Card;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MovementOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
        $movemntsday = StockMovement::whereDate('created_at', $now->today())->count();
        $movemntsmonth = StockMovement::whereMonth('created_at', $now->today())->count();
        $movementsweek = StockMovement::whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->count();


            return [
                Stat::make('Movimientos del dia', $movemntsday)
                    ->icon('heroicon-o-inbox')
                    ->description('Total de Movimientos del dia')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->chart([2,10,3,12,1,14,10,1,2,10])
                ,
                Stat::make('Movimientos de la semana', $movementsweek)
                ->icon('heroicon-o-inbox')
                ->description('Total de Movimientos de la semana')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
                Stat::make('Movimientos del mes', $movemntsmonth)
                    ->icon('heroicon-o-inbox')
                    ->description('Total de Movimientos del mes')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->chart([2,10,3,12,1,14,10,1,2,10])
                ,
            ];
    }
}
