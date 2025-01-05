<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('personal_id');
            $table->integer('cantidad_movimiento');
            $table->timestamps();
            $table->foreign('stock_id')->references('id')->on('stocks');
            $table->foreign('personal_id')->references('id')->on('personals');
            $table->date('fecha_movimiento');
            $table->string('observaciones')->nullable();
            $table->string('marca')->nullable();
            $table->string('certificacion')->nullable();
            $table->string('tipo')->nullable();
            $table->text('firma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
