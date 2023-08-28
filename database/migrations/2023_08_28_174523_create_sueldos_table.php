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
        Schema::create('sueldos', function (Blueprint $table) {
            $table->id();
            $table->integer('monto');
            $table->date('fecha');
            $table->unsignedBigInteger('personal_id');
            $table->foreign('personal_id')->references('id')->on('personals');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sueldos');
    }
};
