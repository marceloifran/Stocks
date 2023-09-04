<?php

namespace App\Filament\Resources\MovementsProductsResource\Pages;

use App\Filament\Resources\MovementsProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovementsProducts extends ListRecords
{
    protected static string $resource = MovementsProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
