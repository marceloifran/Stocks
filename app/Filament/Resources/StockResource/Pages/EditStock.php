<?php

namespace App\Filament\Resources\StockResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StockResource;

class EditStock extends EditRecord
{
    protected static string $resource = StockResource::class;

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
            Actions\CreateAction::make('Variacion')->url(fn() => route('pdf.byStock',['record' => $this->record]))->icon('heroicon-o-document')->label('Variacion'),


        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Stock Actualizado')
            ->body('El stock fue actualizado correctamente y pase a '.$this->record->cantidad);
    }
}
