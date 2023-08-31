<?php

namespace App\Filament\Resources\EquiposResource\Widgets;

use App\Models\equipos;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class EquiposChart extends ChartWidget
{
    protected static ?string $heading = 'Equipos agregadas en el mes';

    protected function getData(): array
    {
        $data = Trend::model(equipos::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Equipos agregados',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
