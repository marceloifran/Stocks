<?php

namespace App\Filament\Resources\SueldoResource\Pages;

use App\Filament\Resources\SueldoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class EditSueldo extends EditRecord
{
    protected static string $resource = SueldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Comprobante')->url(fn() => route('sueldo.comprobante',['record' => $this->record]))->icon('heroicon-o-document')->label('Comprobante'),

        ];
    }
    
}
