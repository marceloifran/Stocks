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
        Schema::table('huella_carbono', function (Blueprint $table) {
            // Verificar si existe la clave foránea antes de eliminarla
            $foreignKeys = \Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'huella_carbono'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME = 'huella_carbono_obra_id_foreign'"
            );

            if (count($foreignKeys) > 0) {
                $table->dropForeign(['obra_id']);
            }

            // Verificar si existe la columna antes de eliminarla
            if (Schema::hasColumn('huella_carbono', 'obra_id')) {
                $table->dropColumn('obra_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('huella_carbono', function (Blueprint $table) {
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('cascade');
        });
    }
};
