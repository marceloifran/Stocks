<?php

namespace App\Filament\Resources\CheckListResource\Pages;

use App\Filament\Resources\CheckListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCheckList extends CreateRecord
{
    protected static string $resource = CheckListResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
