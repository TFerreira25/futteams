<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->tinyInteger('team_number'); // 1 ou 2
            $table->integer('goals')->default(0);
            $table->integer('goals_against')->default(0);
            $table->timestamps();

            $table->unique(['game_id', 'team_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
