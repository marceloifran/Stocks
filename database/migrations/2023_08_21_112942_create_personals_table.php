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
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rol')->nullable();
            // $table->date('fecha_entrada')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            // $table->string('direccion')->nullable();
            // $table->integer('telefono')->nullable();
            $table->string('dni')->nullable();
            $table->string('nro_identificacion')->nullable(); 
            $table->text('firma')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personals');
    }
};
