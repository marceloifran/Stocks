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
            $table->enum('tipo_roster', ['14x14', '21x7', '28x14', 'fijo'])->default('14x14');
            $table->date('fecha_inicio_roster')->nullable();
            $table->enum('estado_roster', ['trabajando', 'descansando', 'inactivo'])->default('inactivo');
            $table->date('proxima_rotacion')->nullable();
            $table->integer('dias_trabajados_consecutivos')->default(0);
            $table->integer('dias_descanso_consecutivos')->default(0);
            $table->boolean('disponible_para_asignacion')->default(true);
            $table->text('observaciones_roster')->nullable();
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
                'tipo_roster',
                'fecha_inicio_roster',
                'estado_roster',
                'proxima_rotacion',
                'dias_trabajados_consecutivos',
                'dias_descanso_consecutivos',
                'disponible_para_asignacion',
                'observaciones_roster'
            ]);
        });
    }
};
