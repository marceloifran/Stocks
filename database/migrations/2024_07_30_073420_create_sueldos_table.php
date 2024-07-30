<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSueldosTable extends Migration
{
    public function up()
    {
        Schema::create('sueldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personals')->onDelete('cascade');
            $table->integer('mes');
            $table->integer('anio');
            $table->integer('horas_normales')->default(0);
            $table->integer('horas_extras')->default(0);
            $table->decimal('pago_horas_normales', 8, 2);
            $table->decimal('pago_horas_extras', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sueldos');
    }
}
