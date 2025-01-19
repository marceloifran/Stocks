<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\personal;
use App\Models\asistencia;
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

       $totalpersonal = 
        $totalstock = stock::all()->count();


        return [
            Stat::make('Bienvenido/a Ifsin', auth()->user()->name)
                ->icon('heroicon-o-user-group'),
            Stat::make('Total Personal', $totalpersonal)
                ->icon('heroicon-o-users')
                ->description('Total de personal')
                ->descriptionIcon('heroicon-o-users', IconPosition::Before),

            Stat::make('Total de Stock', $totalstock)
                ->icon('heroicon-o-inbox-stack')
                ->description('Total de Stock')
                ->descriptionIcon('heroicon-o-inbox-stack',IconPosition::Before)

        ];
    }
}
