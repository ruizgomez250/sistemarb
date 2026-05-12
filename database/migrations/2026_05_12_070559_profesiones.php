<?php
// database/migrations/2026_05_12_000002_create_profesiones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesiones', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150);
            $table->text('observacion')->nullable()->comment('Observación opcional');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesiones');
    }
};