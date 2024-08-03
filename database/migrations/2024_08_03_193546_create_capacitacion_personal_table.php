<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('capacitacion_personal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capacitacion_id');
            $table->unsignedBigInteger('personal_id');
            $table->timestamps();

            $table->foreign('capacitacion_id')
                ->references('id')
                ->on('capacitacions')
                ->onDelete('cascade');

            $table->foreign('personal_id')
                ->references('id')
                ->on('personals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitacion_personal');
    }
};
