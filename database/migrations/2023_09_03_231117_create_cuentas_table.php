<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasTable extends Migration
{
    public function up()
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('saldo', 10, 2)->default(0);
            $table->text('tipo');
            $table->boolean('activo')->default(true);
            // Otros campos de la cuenta financiera
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuentas');
    }
}
