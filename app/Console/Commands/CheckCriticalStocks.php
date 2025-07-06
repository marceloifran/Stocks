<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use App\Models\stock as Stock;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCriticalStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:check-critical';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica stocks críticos y genera órdenes de compra automáticamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando stocks críticos...');

        // Obtener todos los stocks con cantidad <= 10
        $criticalStocks = Stock::where('cantidad', '<=', 10)->get();

        $count = 0;
        foreach ($criticalStocks as $stock) {
            // Verificar si ya existe una orden de compra pendiente o pedida
            $pendingOrders = PurchaseOrder::where('stock_id', $stock->id)
                ->whereIn('status', ['pendiente', 'pedido'])
                ->count();

            if ($pendingOrders === 0) {
                // Crear una nueva orden de compra
                PurchaseOrder::create([
                    'stock_id' => $stock->id,
                    'quantity' => 20, // Cantidad por defecto para reposición
                    'status' => 'pendiente',
                    'requested_date' => Carbon::now(),
                    'notes' => 'Orden generada automáticamente por nivel bajo de stock',
                ]);

                $count++;
                $this->line("Orden de compra creada para: {$stock->nombre} (ID: {$stock->id})");
            }
        }

        $this->info("Proceso completado. Se generaron {$count} órdenes de compra.");
    }
}
