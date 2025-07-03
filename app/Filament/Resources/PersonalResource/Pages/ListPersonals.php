<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonalResource;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\PersonalResource\Widgets\PersonalChart;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use App\Models\personal;
use Filament\Actions\CreateAction;

class ListPersonals extends ListRecords
{
    protected static string $resource = PersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // \EightyNine\ExcelImport\ExcelImportAction::make()
            // ->color("primary"),
            Actions\CreateAction::make()->label(trans('actions.new_person'))->icon('heroicon-o-plus'),
            Actions\CreateAction::make('Tomar Asistencia')->url(fn() => route('asistencia.iniciar'))->label(trans('actions.take_attendance'))->icon('heroicon-o-camera')->color('success'),
            Actions\CreateAction::make('Asistencia del dia')->url(fn() => route('asistencia.dia'))->label(trans('actions.report'))->color('danger'),
            Actions\CreateAction::make('Tomar Comida')->url(fn() => route('comida.iniciar'))->label('Registrar Comidas')->icon('heroicon-o-cake')->color('primary'),
            Actions\CreateAction::make('Reporte de Comidas')->url(fn() => route('comida.reporte'))->label('Reporte de Comidas')->icon('heroicon-o-document')->color('info'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-o-users'),
            'administracion' => Tab::make('Administración')
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Administración')),
            'produccion' => Tab::make('Producción')
                ->icon('heroicon-o-wrench-screwdriver')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Producción')),
            'logistica' => Tab::make('Logística')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Logística')),
            'ventas' => Tab::make('Ventas')
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Ventas')),
            'rrhh' => Tab::make('Recursos Humanos')
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Recursos Humanos')),
            'ti' => Tab::make('TI')
                ->icon('heroicon-o-computer-desktop')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'TI')),
            'otro' => Tab::make('Otro')
                ->icon('heroicon-o-question-mark-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('departamento', 'Otro')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
