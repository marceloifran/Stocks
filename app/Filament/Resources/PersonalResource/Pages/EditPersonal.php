<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonal extends EditRecord
{
    protected static string $resource = PersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
          Actions\CreateAction::make('299')->url(fn() => route('personal.exportPdf',['record' => $this->record]))->icon('heroicon-o-document')->label('299'),
        ];
    }
}
