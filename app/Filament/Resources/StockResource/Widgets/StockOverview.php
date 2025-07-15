<?php

namespace App\Filament\Resources\StockResource\Widgets;

use Carbon\Carbon;
use App\Models\stock;
use App\Models\StockHistory;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalStock = Stock::all()->count();

        $lastMonth = Carbon::now()->subMonth();
        $totalStockLastMonth = Stock::where('created_at', '>=', $lastMonth)->sum('cantidad');
        $lastday = Carbon::now()->subDay();
        $totalstocklastday = Stock::where('created_at', '>=', $lastday)->sum('cantidad');

        $historyCount = StockHistory::all()->count();

        // Generar datos para gráficos más suaves
        $chartTotal = $this->generateSmoothData(10);
        $chartMensual = $this->generateSmoothData(10);
        $chartDiario = $this->generateSmoothData(10);

        return [
            Card::make('Total Stock', $totalStock)
                ->icon('heroicon-o-cube')
                ->description('Total de Stock Registrado')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('primary')
                ->chart($chartTotal),
            Card::make('Stock Total Mensual', $totalStockLastMonth)
                ->icon('heroicon-o-calendar')
                ->description('Total de Stock Registrado en el ultimo mes')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('success')
                ->chart($chartMensual),

            Card::make('Stock Total Diario', $totalstocklastday)
                ->icon('heroicon-o-clock')
                ->description('Total de Stock Registrado en el ultimo dia')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('warning')
                ->chart($chartDiario),
        ];
    }

    /**
     * Genera datos suavizados para gráficos más atractivos
     *
     * @param int $points Número de puntos a generar
     * @return array
     */
    protected function generateSmoothData(int $points): array
    {
        $result = [];

        // Generar puntos con una curva más natural
        for ($i = 0; $i < $points; $i++) {
            // Usar una función sinusoidal para crear ondulaciones naturales
            $factor = sin($i / ($points - 1) * M_PI);
            $value = 5 + 10 * $factor;
            $result[] = round($value);
        }

        return $result;
    }
}
