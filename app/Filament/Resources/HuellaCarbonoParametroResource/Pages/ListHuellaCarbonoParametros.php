<?php

namespace App\Filament\Resources\HuellaCarbonoParametroResource\Pages;

use App\Filament\Resources\HuellaCarbonoParametroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHuellaCarbonoParametros extends ListRecords
{
    protected static string $resource = HuellaCarbonoParametroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
