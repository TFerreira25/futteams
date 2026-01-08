<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->boolean('present')->default(true); // Marca presença
            $table->timestamps();

            $table->unique(['game_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_player');
    }
};
