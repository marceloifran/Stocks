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
        $mesActual = Carbon::now()->month;

        // Calculamos el total de asistencias en el mes actual
        $totalAsistencias = asistencia::whereMonth('fecha', $mesActual)->count();

        // Calculamos el total de faltas en el mes actual restando el total de asistencias del total de personal
        $totalFaltas = $totalPersonal - $totalAsistencias;

        // Calculamos el porcentaje de asistencia en el mes
        $porcentajeAsistencia = ($totalAsistencias / $totalPersonal) * 100;

        // Calculamos el porcentaje de faltas en el mes
        $porcentajeFaltas = ($totalFaltas / $totalPersonal) * 100;

        return [
            Card::make('Total Personal', $totalPersonal)
                ->icon('heroicon-o-inbox')
                ->description('Total de personal Registrado')
                ->descriptionIcon('heroicon-o-information-circle')

            ,
            Card::make('Porcentaje de Asistencia en el Mes', number_format($porcentajeAsistencia, 2) . '%')
                ->icon('heroicon-o-users')
                ->description('Porcentaje de asistencia en el mes actual')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
                ->chartColor('success')
            ,
            Card::make('Porcentaje de Faltas en el Mes', number_format($porcentajeFaltas, 2) . '%')
                ->icon('heroicon-o-calendar')
                ->description('Porcentaje de faltas en el mes actual')
                ->descriptionIcon('heroicon-o-information-circle')
                ->chart([2,10,3,12,1,14,10,1,2,10])
                ->chartColor('')
            ,
        ];
    }
}
