<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['game_id', 'team_number', 'goals', 'goals_against'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function playerGameStatistics(): HasMany
    {
        return $this->hasMany(PlayerGameStatistic::class);
    }

    /**
     * Retorna jogadores da equipa
     */
    public function players()
    {
        return $this->playerGameStatistics()
            ->with('player')
            ->get()
            ->pluck('player');
    }

    /**
     * Conta de jogadores
     */
    public function countPlayers(): int
    {
        return $this->playerGameStatistics()->count();
    }

    /**
     * Total de golos da equipa
     */
    public function getTotalGoals(): int
    {
        return $this->playerGameStatistics()->sum('goals');
    }

    /**
     * Total de assistências da equipa
     */
    public function getTotalAssists(): int
    {
        return $this->playerGameStatistics()->sum('assists');
    }
}
