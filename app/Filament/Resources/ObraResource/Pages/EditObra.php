<?php

namespace App\Filament\Resources\ObraResource\Pages;

use App\Filament\Resources\ObraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditObra extends EditRecord
{
    protected static string $resource = ObraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make('Variacion')->url(fn() => route('pdf.byobra',['record' => $this->record]))->icon('heroicon-o-document')->label('Obra'),
        ];
    }
}
