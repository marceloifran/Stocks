<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\personal;
use App\Models\asistencia;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalCount extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Bienvenido/a', auth()->user()->name)
                ->icon('heroicon-o-user-group')
                ->description('Comprehensive Management System')
                ->descriptionColor('success'),
        ];
    }
}
