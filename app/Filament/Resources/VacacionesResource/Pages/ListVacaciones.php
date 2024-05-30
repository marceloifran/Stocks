<?php

namespace App\Filament\Resources\VacacionesResource\Pages;

use App\Filament\Resources\VacacionesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVacaciones extends ListRecords
{
    protected static string $resource = VacacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
