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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('contratista');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->json('tipo_trabajo'); // Campo json para guardar como array
            $table->boolean('capacitados')->default(false);
            $table->json('trabajadores')->nullable(); // Campo json para guardar como array
            $table->text('trabajos_a_realizar')->nullable();
            $table->text('equipos_a_intervenir')->nullable();
            $table->json('elementos')->nullable(); // Campo json para guardar como array
            $table->dateTime('fecha_a_c')->nullable();
            $table->json('cierre')->nullable(); // Campo json para guardar como array
            $table->dateTime('fecha_fin_pte')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
