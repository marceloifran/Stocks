<?php

namespace App\Filament\Resources\HuellaCarbonoResource\Pages;

use App\Filament\Resources\HuellaCarbonoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHuellaCarbonos extends ListRecords
{
    protected static string $resource = HuellaCarbonoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
