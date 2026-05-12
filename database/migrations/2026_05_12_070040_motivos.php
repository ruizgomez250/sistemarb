<?php
// database/migrations/2026_05_12_000001_create_motivos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 200);
            $table->text('observacion')->nullable()->comment('Observación opcional');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motivos');
    }
};