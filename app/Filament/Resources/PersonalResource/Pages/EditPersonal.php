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
          Actions\CreateAction::make('299')->url(fn() => route('personal.exportPdf',['record' => $this->record]))->icon('heroicon-o-document')->label('299'),
           Actions\DeleteAction::make('asistencia')->url(fn() => route('asistencia.personal',['record' => $this->record]))->icon('heroicon-o-user')->label('Asistencia'),
        ];

        // Route::get('asistencia-personal/{record}', [QRCodeController::class, 'personal'])->name('asistencia.personal');
    }




}
