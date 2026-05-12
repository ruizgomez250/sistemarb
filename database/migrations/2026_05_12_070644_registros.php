<?php
// database/migrations/2026_05_12_000003_create_registros_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 20)->unique()->comment('Cédula única por persona');
            $table->string('nombres_y_apellidos', 200);
            $table->string('telefono1', 20);
            $table->string('telefono2', 20)->nullable();
            $table->string('telefono3', 20)->nullable();
            $table->text('direccion')->nullable()->comment('Campo opcional');
            $table->string('barrio', 100);
            $table->text('observacion_general')->nullable()->comment('Observación general opcional');
            $table->foreignId('motivo_id')->constrained('motivos')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('fecha_nacimiento');
            $table->string('afiliacion', 100);
            $table->foreignId('profesion_id')->constrained('profesiones')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('local_interna', 100);
            $table->string('local_generales', 100);
            $table->timestamps();
            
            // Índices para búsquedas frecuentes
            $table->index('cedula');
            $table->index('nombres_y_apellidos');
            $table->index('barrio');
            $table->index('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};