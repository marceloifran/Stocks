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
        $totalmovemnts = StockMovement::all()->count();
        $lastday = StockMovement::whereDate('created_at', $now->today())->count();
        $lastmonth = StockMovement::whereMonth('created_at', $now->today())->count();

        $movemntsday = StockMovement::whereDate('created_at', $now->today())->count();
        $movemntsmonth = StockMovement::whereMonth('created_at', $now->today())->count();


            return [
                Card::make('Movimientos del dia', $movemntsday)
                    ->icon('heroicon-o-inbox')
                    ->description('Total de Movimientos del dia')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->chart([2,10,3,12,1,14,10,1,2,10])
                ,
                Card::make('Movimientos del mes', $movemntsmonth)
                    ->icon('heroicon-o-inbox')
                    ->description('Total de Movimientos del mes')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->chart([2,10,3,12,1,14,10,1,2,10])
                ,
            ];
    }
}
