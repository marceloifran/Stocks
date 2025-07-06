<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero asegurarse de que la columna existe
        if (!Schema::hasColumn('purchase_orders', 'order_column')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->integer('order_column')->nullable();
            });
        }

        // Actualizar los registros existentes agrupados por status
        $statuses = ['pendiente', 'pedido', 'comprado', 'recibido'];

        foreach ($statuses as $status) {
            $orders = DB::table('purchase_orders')
                ->where('status', $status)
                ->orderBy('id')
                ->get();

            $order = 1;
            foreach ($orders as $purchaseOrder) {
                DB::table('purchase_orders')
                    ->where('id', $purchaseOrder->id)
                    ->update(['order_column' => $order]);
                $order++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir nada, ya que solo estamos actualizando valores
    }
};
