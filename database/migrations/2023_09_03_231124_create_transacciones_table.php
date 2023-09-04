<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionesTable extends Migration
{
    public function up()
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuenta_id');
            $table->decimal('monto', 10, 2);
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->enum('tipo', ['ingreso', 'gasto']);
            // Otros campos de la transacción financiera
            $table->timestamps();

            $table->foreign('cuenta_id')->references('id')->on('cuentas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones');
    }
}
