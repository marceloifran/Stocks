<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('codigo_barras')->unique();
            $table->integer('cantidad_stock')->default(0);
            $table->integer('cantidad_minima')->default(0);
            $table->decimal('precio_compra', 8, 2)->default(0);
            $table->decimal('precio_venta', 8, 2)->default(0);
            $table->string('proveedor')->nullable();
            $table->string('ubicacion_almacen')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
