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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('patente');
            $table->string('tipo');
            $table->string('estado');
            $table->string('seguro');
            $table->string('rto');
            $table->string('poliza');
            $table->date('fecha_ultimo_mantenimiento');
            $table->foreignId('personal_id')->constrained('personals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
