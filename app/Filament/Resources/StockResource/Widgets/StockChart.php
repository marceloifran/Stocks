<?php
namespace App\Filament\Resources\StockResource\Widgets;

use App\Models\stock;
use Filament\Widgets\LineChartWidget;

class StockChart extends LineChartWidget
{
    protected static ?string $heading = 'Variación de Stock a lo Largo del Tiempo';

    protected function getData(): array
    {
        $stocks = Stock::all();
        $dates = []; // Almacenar las fechas únicas para etiquetas del gráfico
        $datasets = [];
        $colorPalette = ['#007BFF', '#28A745', '#DC3545', '#FFC107', '#17A2B8']; // Paleta de colores

        foreach ($stocks as $index => $stock) {
            $historyData = [];

            // Obtener el historial de cambios de stock para el producto actual
            foreach ($stock->stockhistory()->orderBy('fecha_nueva')->get() as $history) {
                $date = $history->fecha_nueva; // Fecha del historial

                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }

                // Utilizar el valor nuevo de stock para la fecha actual
                $historyData[$date] = $history->valor_nuevo;
            }

            $data = [];
            foreach ($dates as $date) {
                $data[] = $historyData[$date] ?? 0;
            }

            $colorIndex = $index % count($colorPalette);
            $datasets[] = [
                'label' => $stock->nombre,
                'data' => $data,
                'borderColor' => $colorPalette[$colorIndex],
                'backgroundColor' => 'rgba(0, 0, 0, 0)', // Sin fondo
                'borderWidth' => 2,
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
