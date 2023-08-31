<?php

namespace App\Filament\Resources\StockResource\Widgets;

use App\Models\stock;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Stock Creados en el mes';

    protected function getData(): array
    {
        $data = Trend::model(stock::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Stocks Creados',
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
