<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use Filament\Actions;
use App\Models\personal;
use Filament\Resources\Pages\EditRecord;
use Filament\Widgets\StatsOverviewWidget\Card;

use App\Filament\Resources\PersonalResource;

class EditPersonal extends EditRecord
{
    protected static string $resource = PersonalResource::class;

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
          Actions\CreateAction::make('299')->url(fn() => route('personal.exportPdf',['record' => $this->record]))->icon('heroicon-o-document')->label('299'),


    // ,
        ];

    }




}
