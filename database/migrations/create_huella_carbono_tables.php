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
        Schema::create('huella_carbono', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('total_emisiones', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('huella_carbono_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('huella_carbono_id')->constrained('huella_carbono')->onDelete('cascade');
            $table->string('tipo_fuente'); // combustible, electricidad, residuos
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad'); // litros, kWh, kg
            $table->decimal('emisiones_co2', 10, 2);
            $table->decimal('factor_conversion', 10, 6);
            $table->json('detalles')->nullable(); // Información adicional como tipo de vehículo, tipo de residuo, etc.
            $table->timestamps();
        });

        Schema::create('huella_carbono_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('categoria'); // combustible, electricidad, residuos
            $table->string('tipo'); // gasolina, diesel, electricidad_red, papel, etc.
            $table->string('descripcion');
            $table->decimal('factor_conversion', 10, 6);
            $table->string('unidad_medida');
            $table->string('unidad_resultado')->default('kgCO2e');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huella_carbono_detalles');
        Schema::dropIfExists('huella_carbono');
        Schema::dropIfExists('huella_carbono_parametros');
    }
};
