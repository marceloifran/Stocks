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
            Actions\CreateAction::make()->label('Nueva Persona')->icon('heroicon-o-plus'),
            //  Actions\CreateAction::make('Generar Qr')->url(fn() => route('qrcode.generateBulkQRs'))->label('Qr'),
            Actions\CreateAction::make('Tomar Asistencia')->url(fn() => route('asistencia.iniciar'))->label('Tomar Asistencia')->color('danger'),
            Actions\CreateAction::make('Asistencia del Dia')->url(fn() => route('asistencia.dia'))->label('Dia'),
            Actions\CreateAction::make('Horas')->url(fn() => route('horas.iniciar'))->label('Tomar Horas')->color('danger'),
            Actions\CreateAction::make('Horas del Dia')->url(fn() => route('horas.dia'))->label('Horas'),

        ];
    }

    public function getTabs(): array
    {
        return [
            'Todo el Personal' => Tab::make()
                ->icon('heroicon-o-users')
                ->badge(personal::all()->count()),
            // 'Ingenieros/as' => Tab::make()
            //     ->icon('heroicon-o-user')
            //     ->badge(personal::where('rol', 'Ingeniero/a')->count())
            //    ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'Ingeniero/a')),
            // 'HyS' => Tab::make()
            //     ->icon('heroicon-o-user')
            //     ->badge(personal::where('rol', 'HyS')->count())
            //      ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'HyS')),
            'Ayudantes' => Tab::make()
                ->icon('heroicon-o-user-group')
                ->badge(personal::where('rol', 'Ayudante')->count())
               ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'Ayudantes')),
            'Oficiales' => Tab::make()
                ->icon('heroicon-o-user-group')
                ->badge(personal::where('rol', 'Oficial')->count())
                  ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'Oficial')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
             PersonOverview::class,
            // PersonalChart::class
        ];
    }
}
