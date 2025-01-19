<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use Filament\Actions;
use App\Models\StockMovement;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StockMovementResource;

class EditStockMovement extends EditRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->getRecord();
                    $record->delete();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}


