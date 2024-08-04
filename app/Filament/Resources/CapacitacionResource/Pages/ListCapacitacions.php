<?php

namespace App\Filament\Resources\CapacitacionResource\Pages;

use App\Filament\Resources\CapacitacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCapacitacions extends ListRecords
{
    protected static string $resource = CapacitacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Capacitacion')->icon('heroicon-o-plus'),
        ];
    }
}
