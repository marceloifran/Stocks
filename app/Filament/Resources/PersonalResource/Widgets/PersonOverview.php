<?php

namespace App\Filament\Resources\PersonalResource\Widgets;

use Carbon\Carbon;
use App\Models\personal;
use App\Models\asistencia;

use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPersonal = personal::all()->count();
        //presentes en el dia
        $presentes = asistencia::where('fecha', Carbon::now()->format('Y-m-d'))->where('presente', true)->count();
        //faltas en el dia
        $faltas = asistencia::where('fecha', Carbon::now()->format('Y-m-d'))->where('presente', false)->count();

        return [
            Card::make('Total Personal', $totalPersonal)
                ->icon('heroicon-o-inbox')
                ->description('Total de personal Registrado')
                ->descriptionIcon('heroicon-o-information-circle')

            ,
            // Card::make('Presentes el dia de hoy', number_format($presentes) )
            //     ->icon('heroicon-o-users')
            //     ->descriptionIcon('heroicon-o-information-circle')
            //     ->description('Total de personal Presente el dia de hoy')
            // ,
            // Card::make('Ausentes el dia de hoy', number_format($faltas))
            //     ->icon('heroicon-o-calendar')
            //     ->description('Total de personal Ausente el dia de hoy')
            //     ->descriptionIcon('heroicon-o-information-circle')
            // ,
        ];
    }
}
