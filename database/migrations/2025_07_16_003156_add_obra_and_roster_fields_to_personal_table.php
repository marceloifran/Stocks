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
        Schema::table('personals', function (Blueprint $table) {
            $table->foreignId('obra_actual_id')->nullable()->constrained('obras')->onDelete('set null');
            $table->boolean('disponible_para_asignacion')->default(true);
            $table->text('observaciones_obra')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personals', function (Blueprint $table) {
            $table->dropForeign(['obra_actual_id']);
            $table->dropColumn([
                'obra_actual_id',
                'disponible_para_asignacion',
                'observaciones_obra'
            ]);
        });
    }
};
