<?php

namespace App\Filament\Resources\CheckListResource\Pages;

use App\Filament\Resources\CheckListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCheckLists extends ListRecords
{
    protected static string $resource = CheckListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
