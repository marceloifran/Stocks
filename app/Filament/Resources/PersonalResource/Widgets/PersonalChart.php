<?php

namespace App\Filament\Resources\PersonalResource\Widgets;

use App\Models\personal;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class PersonalChart extends ChartWidget
{

    protected static ?string $heading = 'Personas agregadas en el mes';

    protected function getData(): array
    {
        $data = Trend::model(personal::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Personas agregadas',
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
