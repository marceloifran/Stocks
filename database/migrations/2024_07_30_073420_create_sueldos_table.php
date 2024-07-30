<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSueldosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sueldos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personal_id');
            $table->date('fecha');
            $table->integer('horas_normales');
            $table->integer('horas_extras');
            $table->decimal('pago_horas_normales', 8, 2);
            $table->decimal('pago_horas_extras', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();

            $table->foreign('personal_id')
            ->references('id')
            ->on('personals')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sueldos');
    }
}
