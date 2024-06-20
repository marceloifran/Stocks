<?php

namespace App\Filament\Resources\MatafuegosResource\Pages;

use App\Filament\Resources\MatafuegosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQrCode extends ViewRecord
{
    protected static string $resource = MatafuegosResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.view-qr-code';

    protected function getHeaderActions(): array
    {
        return [];
    }
}