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
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personals')->onDelete('cascade');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->date('fecha_inicio_trabajo');
            $table->date('fecha_fin_trabajo');
            $table->date('fecha_inicio_descanso');
            $table->date('fecha_fin_descanso');
            $table->enum('estado_actual', ['trabajando', 'descansando', 'finalizado'])->default('trabajando');
            $table->integer('ciclo_numero')->default(1); // Para llevar el control de ciclos
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices para mejorar consultas
            $table->index(['personal_id', 'fecha_inicio_trabajo']);
            $table->index(['obra_id', 'estado_actual']);
            $table->index(['fecha_inicio_trabajo', 'fecha_fin_trabajo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};
