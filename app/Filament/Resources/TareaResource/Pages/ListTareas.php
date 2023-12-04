<?php

namespace App\Filament\Resources\TareaResource\Pages;

use App\Filament\Resources\TareaResource;
use App\Models\tarea;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTareas extends ListRecords
{
    protected static string $resource = TareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->icon('heroicon-o-inbox-stack')
                ->badge(tarea::all()->count()),
            'Earring' => Tab::make()
                ->icon('heroicon-o-x-circle')
                //forward
                ->badge(tarea::where('estado', 'Finalizado' )->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Pendiente' )),
                'Process' => Tab::make()
                ->icon('heroicon-o-forward')
                //forward
                ->badge(tarea::where('estado', 'Proceso' )->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Proceso' )),
            'Finalized' => Tab::make()
                ->icon('heroicon-o-check-circle')
                ->badge(tarea::where('estado', 'Finalizado' )->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Finalizado' )),
        ];
    }
}
