<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\personal;
use App\Models\asistencia;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalCount extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        $totalausenteshoy = asistencia::where('presente', 0)
            ->whereDate('fecha', $today)
            ->where('estado', 'entrada')
            ->count();

        $totalpresenteshoy = asistencia::where('presente', 1)
            ->whereDate('fecha', $today)
            ->where('estado', 'entrada')
            ->count();
        $totalstock = stock::all()->count();


        return [
            Card::make('Total Personal presente hoy', $totalpresenteshoy)
                ->icon('heroicon-o-users')
                ->description('Total de personal presente hoy')
                ->descriptionIcon('heroicon-o-users')
                ->descriptionColor('success')
            ,
            Card::make('Total de Personal ausente hoy', $totalausenteshoy)
                ->icon( 'heroicon-o-users')
                ->description('Total de personal ausente hoy')
                ->descriptionIcon( 'heroicon-o-users')
                ->descriptionColor('danger')
            ,
            Card::make('Total de Stock', $totalstock)
                ->icon('heroicon-o-inbox-stack')
                ->description('Total de Stock Registrado')
            ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-inbox-stack')
            ,

            Card::make('Bienvenido/a ', auth()->user()->name)
                ->icon('heroicon-o-user-group')
                ->description('Comprehensive Management System')
                ->descriptionColor('success')

            

        ];
    }
}
