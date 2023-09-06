<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Models\User;
use App\Models\stock;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StockMovementResource;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;


    protected function getRedirectUrl(): string
    {
        $stockname = stock::find($this->record->stock_id)->nombre;
        Notification::make()
        ->title('Nuevo movimiento en '.$stockname)
        ->body('Se ha creado un nuevo movimiento de stock')
        ->success()
        ->sendToDatabase(User::whereNotNull('email_verified_at')->get());
        return $this->getResource()::getUrl('index');

    }



}
