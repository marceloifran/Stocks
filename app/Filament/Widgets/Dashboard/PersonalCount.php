<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\equipos;
use App\Models\personal;
use App\Models\producto;
use App\Models\asistencia;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalCount extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        $totalausenteshoy = Asistencia::where('estado', 'Ausente')
            ->whereDate('created_at', $today)
            ->count();

        $totalpresenteshoy = Asistencia::where('estado', 'Presente')
            ->whereDate('created_at', $today)
            ->count();
        $totalstock = stock::all()->count();
        $totalequipos = equipos::all()->count();
        $totalproductos = producto::all()->count();


        return [
            Card::make('Total Personal presente hoy', $totalpresenteshoy)
                ->icon('heroicon-o-users')
                ->description('Total de personal presente hoy')
                ->descriptionIcon('heroicon-o-users')
                ->descriptionColor('success')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
            Card::make('Total de Personal ausente hoy', $totalausenteshoy)
                ->icon( 'heroicon-o-users')
                ->description('Total de equipos Registrados')
                ->descriptionIcon( 'heroicon-o-users')
                ->descriptionColor('danger')
                ->chart([2,10,3,12,1,14,10,1,2,10])
            ,
            // Card::make('Total de Stock', $totalstock)
            //     ->icon('heroicon-o-inbox-stack')
            //     ->description('Total de Stock Registrado')
            // ->descriptionColor('success')
            //     ->descriptionIcon('heroicon-o-inbox-stack')
            //     ->chart([2,10,3,12,1,14,10,1,2,10])
            // ,

            Card::make('Bienvenido/a ', auth()->user()->name)
                ->icon('heroicon-o-user-group')
                ->description('Sistema de Gestion Integral')
                ->descriptionIcon('heroicon-o-user-group')
                ->descriptionColor('success')
                ->chart([2,10,3,12,1,14,10,1,2,10])

            // Card::make('Total de Productos', $totalproductos)
            //     ->icon('heroicon-o-inbox-stack')
            //     ->description('Total de Producto')
            // ->descriptionColor('success')
            //     ->descriptionIcon('heroicon-o-inbox-stack')
            //     ->chart([2,10,3,12,1,14,10,1,2,10])
            // ,

        ];
    }
}
