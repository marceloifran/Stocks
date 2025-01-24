<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PersonalResource;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\PersonalResource\Widgets\PersonalChart;
use App\Filament\Resources\PersonalResource\Widgets\PersonOverview;
use App\Models\personal;
use Filament\Actions\CreateAction;

class ListPersonals extends ListRecords
{
    protected static string $resource = PersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(trans('actions.new_person'))->icon('heroicon-o-plus'), 
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
          
        ];
    }
}
