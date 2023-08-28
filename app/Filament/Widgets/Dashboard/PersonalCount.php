<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\stock;
use App\Models\personal;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalCount extends BaseWidget
{
    protected function getStats(): array
    {
        $totalpersonal = personal::all()->count();
        $totalstock = stock::all()->count();


        return [
            Card::make('Total Personal', $totalpersonal)
                ->icon('heroicon-o-users')
                ->description('Total de personal Registrado')
                ->descriptionIcon('heroicon-o-users')
                ->descriptionColor('success')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
            Card::make('Total de Stock', $totalstock)
                ->icon('heroicon-o-inbox-stack')
                ->description('Total de personal Registrado')
            ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-inbox-stack')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,

        ];
    }
}
