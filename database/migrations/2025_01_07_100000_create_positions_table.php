<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'GK', 'DEF', 'MID', 'FWD'
            $table->string('name'); // 'Guarda-redes', 'Defesa', 'Médio', 'Avançado'
            $table->integer('max_per_team')->default(1); // Máximo dessa posição por equipa
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
