<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_game_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');

            // Estatísticas
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('goals_conceded')->nullable(); // Para GK's

            $table->boolean('is_present')->default(true);
            $table->timestamps();

            $table->unique(['player_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_game_statistics');
    }
};
