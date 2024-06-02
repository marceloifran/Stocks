<?php

namespace App\Filament\Resources\MatafuegosResource\Pages;

use App\Filament\Resources\MatafuegosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatafuegos extends ListRecords
{
    protected static string $resource = MatafuegosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
