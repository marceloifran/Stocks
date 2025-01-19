<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
}
