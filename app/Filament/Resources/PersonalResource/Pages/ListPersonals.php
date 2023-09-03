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

class ListPersonals extends ListRecords
{
    protected static string $resource = PersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Persona')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todo el Personal' => Tab::make()
                ->icon('heroicon-o-users')
                ->badge(personal::all()->count()),
            'Ingenieros/as' => Tab::make()
                ->icon('heroicon-o-user')
                ->badge(personal::where('rol', 'Ingeniero/a')->count())
               ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'Ingeniero/a')),
            'HyS' => Tab::make()
                ->icon('heroicon-o-user')
                ->badge(personal::where('rol', 'HyS')->count())
                 ->modifyQueryUsing(fn (Builder $query) => $query->where('rol', 'HyS')),
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
            // PersonOverview::class,
            PersonalChart::class
        ];
    }
}
