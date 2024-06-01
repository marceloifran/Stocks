<?php

namespace App\Filament\Resources\CheckListResource\Pages;

use App\Filament\Resources\CheckListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCheckList extends EditRecord
{
    protected static string $resource = CheckListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make('CheckList')->url(fn() => route('personal.checklist',['record' => $this->record]))->icon('heroicon-o-clipboard-document-check')->label('Check List'),
        ];
    }
}
