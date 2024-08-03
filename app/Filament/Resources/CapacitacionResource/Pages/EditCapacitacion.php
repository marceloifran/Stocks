<?php

namespace App\Filament\Resources\CapacitacionResource\Pages;

use App\Filament\Resources\CapacitacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCapacitacion extends EditRecord
{
    protected static string $resource = CapacitacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make('Capacitacion')->url(fn() => route('personal.capacitacion',['record' => $this->record]))->icon('heroicon-o-clipboard-document-check')->label('Capacitacion'),


        ];
    }
}
