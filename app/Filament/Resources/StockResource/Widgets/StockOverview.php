<?php

namespace App\Filament\Resources\StockResource\Widgets;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\StockHistory;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalStock = Stock::all()->count();

        $lastMonth = Carbon::now()->subMonth();
        $totalStockLastMonth = Stock::where('created_at', '>=', $lastMonth)->sum('cantidad');
        $lastday = Carbon::now()->subDay();
        $totalstocklastday = Stock::where('created_at', '>=', $lastday)->sum('cantidad');

        $historyCount = StockHistory::all()->count();
        // $operationHistoryCount = StockOperation::all()->count();

        return [
            Card::make('Total Stock', $totalStock)
                ->icon('heroicon-o-inbox')
                ->description('Total de Stock Registrado')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
            Card::make('Stock Total Mensual', $totalStockLastMonth)
            ->icon('heroicon-o-inbox')
            ->description('Total de Stock Registrado en el ultimo mes')
            ->descriptionIcon('heroicon-o-information-circle')
            ->chart([2,10,3,12,1,14,10,1,2,10]),
            Card::make('Stock Total Diario', $totalstocklastday)
            ->icon('heroicon-o-inbox')
            ->description('Total de Stock Registrado en el ultimo dia')
            ->descriptionIcon('heroicon-o-information-circle')
            ->chart([2,10,3,12,1,14,10,1,2,10]),

        ];
    }
}
