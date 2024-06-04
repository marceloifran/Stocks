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
        Schema::create('matafuegos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_vencimiento');
            $table->string('ubicacion');
            $table->string('capacidad');
            $table->string('responsable_mantenimiento');
            $table->date('fecha_fabricacion');
            $table->date('fecha_ultima_recarga');
            $table->string('numero_serie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matafuegos');
    }
};
