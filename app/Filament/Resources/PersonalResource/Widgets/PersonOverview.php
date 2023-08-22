<?php

namespace App\Filament\Resources\PersonalResource\Widgets;

use App\Models\personal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

use Filament\Widgets\StatsOverviewWidget\Stat;

class PersonOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalpersonal = personal::all()->count();


        return [
            Card::make('Total Personal', $totalpersonal)
                ->icon('heroicon-o-inbox')
                ->description('Total de personal Registrado')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
        ];
    }
}
