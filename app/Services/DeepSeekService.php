<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Personal;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeepSeekService
{
    public function query(string $question): string
    {
        $question = Str::lower($question);

        // Consultas de stocks bajos
        if (Str::contains($question, ['stock bajo', 'stocks bajos', 'bajo stock', 'crítico'])) {
            return $this->getLowStocks();
        }

        // Consultas de valor total
        if (Str::contains($question, ['valor total', 'total inventario', 'suma total', 'precio total'])) {
            return $this->getTotalStockValue();
        }

        // Consultas de movimientos por fecha
        if (Str::contains($question, ['movimientos', 'movimiento']) &&
            (Str::contains($question, ['hoy', 'ayer', 'semana', 'mes', 'fecha']))) {
            return $this->getMovementsByDate($question);
        }

        // Consultas de personal activo
        if (Str::contains($question, ['personal activo', 'persona más movimientos', 'quien mueve más', 'operario'])) {
            if (Str::contains($question, ['hoy', 'ayer', 'semana', 'mes'])) {
                return $this->getMostActivePersonnelByPeriod($question);
            }
            return $this->getMostActivePersonnel();
        }

        // Consultas de stock específico con fecha
        if (Str::contains($question, ['cuanto hay', 'cantidad', 'stock']) &&
            Str::contains($question, ['de', 'del'])) {
            if (Str::contains($question, ['hoy', 'ayer', 'semana', 'mes', 'fecha'])) {
                return $this->getSpecificStockByDate($question);
            }
            return $this->getSpecificStock($question);
        }

        // Consultas de productos sin movimientos
        if (Str::contains($question, ['sin movimientos', 'sin actividad', 'inactivos', 'estancados'])) {
            return $this->getStocksWithoutMovements();
        }

        // Consultas de productos más movidos
        if (Str::contains($question, ['más movido', 'mayor movimiento', 'más activo'])) {
            return $this->getMostMovedStocks();
        }

        // Consultas de valor por tipo
        if (Str::contains($question, ['valor por tipo', 'total por tipo', 'suma por tipo'])) {
            return $this->getValueByStockType();
        }

        return "No pude entender tu pregunta. Prueba preguntando por:\n" .
               "- Stocks bajos\n" .
               "- Valor total del inventario\n" .
               "- Movimientos por fecha (ejemplo: 'movimientos de hoy/ayer/esta semana')\n" .
               "- Personal más activo (ejemplo: 'personal más activo esta semana')\n" .
               "- Cantidad de un producto (ejemplo: 'cuánto hay de cemento')\n" .
               "- Productos sin movimientos\n" .
               "- Productos más movidos\n" .
               "- Valor por tipo de stock";
    }

    private function getMovementsByDate(string $question): string
    {
        $query = StockMovement::with(['stock', 'personal']);

        if (Str::contains($question, 'hoy')) {
            $query->whereDate('fecha_movimiento', Carbon::today());
        } elseif (Str::contains($question, 'ayer')) {
            $query->whereDate('fecha_movimiento', Carbon::yesterday());
        } elseif (Str::contains($question, 'semana')) {
            $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfWeek(), Carbon::now()]);
        } elseif (Str::contains($question, 'mes')) {
            $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $movements = $query->latest('fecha_movimiento')->take(5)->get();

        if ($movements->isEmpty()) {
            return "No se encontraron movimientos en el período especificado.";
        }

        $response = "Movimientos encontrados:\n";
        foreach ($movements as $movement) {
            $response .= "- {$movement->stock->nombre}: {$movement->cantidad_movimiento} " .
                        "por {$movement->personal->nombre} " .
                        "el {$movement->fecha_movimiento->format('d/m/Y H:i')}\n";
        }
        return $response;
    }

    private function getMostActivePersonnelByPeriod(string $question): string
    {
        $query = Personal::withCount(['stockMovement' => function ($query) use ($question) {
            if (Str::contains($question, 'hoy')) {
                $query->whereDate('fecha_movimiento', Carbon::today());
            } elseif (Str::contains($question, 'ayer')) {
                $query->whereDate('fecha_movimiento', Carbon::yesterday());
            } elseif (Str::contains($question, 'semana')) {
                $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfWeek(), Carbon::now()]);
            } elseif (Str::contains($question, 'mes')) {
                $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfMonth(), Carbon::now()]);
            }
        }]);

        $personal = $query->orderByDesc('stock_movement_count')->first();

        if (!$personal || $personal->stock_movement_count == 0) {
            return "No se encontraron movimientos de personal en el período especificado.";
        }

        $periodo = Str::contains($question, 'hoy') ? "hoy" :
                  (Str::contains($question, 'ayer') ? "ayer" :
                  (Str::contains($question, 'semana') ? "esta semana" : "este mes"));

        return "{$personal->nombre} es la persona más activa {$periodo} con {$personal->stock_movement_count} movimientos.";
    }

    private function getSpecificStockByDate(string $question): string
    {
        $words = explode(' ', $question);
        $productName = '';

        for ($i = 0; $i < count($words); $i++) {
            if ($words[$i] === 'de' || $words[$i] === 'del') {
                $productName = implode(' ', array_slice($words, $i + 1));
                break;
            }
        }

        if (empty($productName)) {
            return "No pude identificar el producto. Por favor, especifica el nombre del producto.";
        }

        $stock = Stock::where('nombre', 'LIKE', "%{$productName}%")->first();

        if (!$stock) {
            return "No encontré ningún stock con el nombre '{$productName}'.";
        }

        $query = StockMovement::where('stock_id', $stock->id);

        if (Str::contains($question, 'hoy')) {
            $query->whereDate('fecha_movimiento', Carbon::today());
        } elseif (Str::contains($question, 'ayer')) {
            $query->whereDate('fecha_movimiento', Carbon::yesterday());
        } elseif (Str::contains($question, 'semana')) {
            $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfWeek(), Carbon::now()]);
        } elseif (Str::contains($question, 'mes')) {
            $query->whereBetween('fecha_movimiento', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $movements = $query->get();
        $totalMovement = $movements->sum('cantidad_movimiento');

        $periodo = Str::contains($question, 'hoy') ? "hoy" :
                  (Str::contains($question, 'ayer') ? "ayer" :
                  (Str::contains($question, 'semana') ? "esta semana" : "este mes"));

        return "Stock actual de {$stock->nombre}: {$stock->cantidad} {$stock->unidad_medida}\n" .
               "Movimientos {$periodo}: {$totalMovement} {$stock->unidad_medida}\n" .
               "Valor actual: $" . number_format($stock->cantidad * $stock->precio, 2);
    }

    private function getStocksWithoutMovements(): string
    {
        $stocks = Stock::whereDoesntHave('stockMovement')
            ->orWhereHas('stockMovement', function ($query) {
                $query->where('fecha_movimiento', '<', Carbon::now()->subMonth());
            })
            ->get();

        if ($stocks->isEmpty()) {
            return "No hay stocks sin movimientos recientes.";
        }

        $response = "Stocks sin movimientos recientes:\n";
        foreach ($stocks as $stock) {
            $response .= "- {$stock->nombre}: {$stock->cantidad} {$stock->unidad_medida}\n";
        }
        return $response;
    }

    private function getMostMovedStocks(): string
    {
        $stocks = Stock::withCount('stockMovement')
            ->orderByDesc('stock_movement_count')
            ->take(5)
            ->get();

        if ($stocks->isEmpty()) {
            return "No hay registros de movimientos de stock.";
        }

        $response = "Productos más movidos:\n";
        foreach ($stocks as $stock) {
            $response .= "- {$stock->nombre}: {$stock->stock_movement_count} movimientos\n";
        }
        return $response;
    }

    private function getValueByStockType(): string
    {
        $types = Stock::select('tipo_stock')
            ->selectRaw('SUM(cantidad * precio) as total_value')
            ->groupBy('tipo_stock')
            ->get();

        if ($types->isEmpty()) {
            return "No hay información de valores por tipo de stock.";
        }

        $response = "Valor total por tipo de stock:\n";
        foreach ($types as $type) {
            $response .= "- {$type->tipo_stock}: $" . number_format($type->total_value, 2) . "\n";
        }
        return $response;
    }

    private function getLowStocks(): string
    {
        $lowStocks = Stock::where('cantidad', '<=', 10)->get();
        if ($lowStocks->isEmpty()) {
            return "No hay stocks bajos en este momento.";
        }

        $response = "Stocks con nivel bajo:\n";
        foreach ($lowStocks as $stock) {
            $response .= "- {$stock->nombre}: {$stock->cantidad} {$stock->unidad_medida}\n";
        }
        return $response;
    }

    private function getTotalStockValue(): string
    {
        $total = Stock::all()->sum(function($stock) {
            return $stock->cantidad * $stock->precio;
        });
        return "El valor total del inventario es: $" . number_format($total, 2);
    }

    private function getMostActivePersonnel(): string
    {
        $personal = Personal::withCount('stockMovement')
            ->orderByDesc('stock_movement_count')
            ->first();

        if (!$personal) {
            return "No hay registros de movimientos de personal.";
        }

        return "{$personal->nombre} es la persona más activa con {$personal->stock_movement_count} movimientos.";
    }

    private function getSpecificStock(string $question): string
    {
        // Buscar el nombre del producto en la pregunta
        $words = explode(' ', $question);
        $productName = '';

        // Buscar después de "de" en la pregunta
        for ($i = 0; $i < count($words); $i++) {
            if ($words[$i] === 'de') {
                $productName = implode(' ', array_slice($words, $i + 1));
                break;
            }
        }

        if (empty($productName)) {
            return "No pude identificar el producto. Por favor, especifica el nombre del producto.";
        }

        $stock = Stock::where('nombre', 'LIKE', "%{$productName}%")->first();

        if (!$stock) {
            return "No encontré ningún stock con el nombre '{$productName}'.";
        }

        return "El stock de {$stock->nombre} es de {$stock->cantidad} {$stock->unidad_medida}. " .
               "Valor total: $" . number_format($stock->cantidad * $stock->precio, 2);
    }
}
