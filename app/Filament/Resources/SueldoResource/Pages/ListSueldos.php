<?php

namespace App\Filament\Resources\SueldoResource\Pages;

use App\Filament\Resources\SueldoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSueldos extends ListRecords
{
    protected static string $resource = SueldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
