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
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            Actions\CreateAction::make('299')->url(fn() => route('personal.exportPdf', ['record' => $this->record]))->icon('heroicon-o-document')->label('299'),
            Actions\Action::make('credencial')->url(fn() => route('personal.credencial.ver', ['id' => $this->record->id]))->icon('heroicon-o-identification')->label('Ver Credencial')->color('info'),
            Actions\DeleteAction::make('asistencia')->url(fn() => route('asistencia.personal', ['record' => $this->record]))->icon('heroicon-o-user')->label('Asistencia')->color('warning'),
            Actions\DeleteAction::make('comidas')->url(fn() => route('comida.personal', ['record' => $this->record]))->icon('heroicon-o-cake')->label('Comidas')->color('primary'),

            // ,
        ];
    }
}
