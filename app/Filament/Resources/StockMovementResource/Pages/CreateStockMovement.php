<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Models\stock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StockMovementResource;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');

    }



}
