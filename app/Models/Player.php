<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = ['name', 'email', 'position_id', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope para jogadores ativos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_player')
            ->withPivot('present')
            ->withTimestamps();
    }

    public function playerGameStatistics(): HasMany
    {
        return $this->hasMany(PlayerGameStatistic::class);
    }

    /**
     * Calcula golos por jogo
     */
    public function goalsPerGame(): float
    {
        $games = $this->playerGameStatistics()->where('is_present', true)->count();
        if ($games === 0) {
            return 0;
        }
        $totalGoals = $this->playerGameStatistics()->sum('goals');
        return round($totalGoals / $games, 2);
    }

    /**
     * Calcula assistências por jogo
     */
    public function assistsPerGame(): float
    {
        $games = $this->playerGameStatistics()->where('is_present', true)->count();
        if ($games === 0) {
            return 0;
        }
        $totalAssists = $this->playerGameStatistics()->sum('assists');
        return round($totalAssists / $games, 2);
    }

    /**
     * Total de jogos com presença
     */
    public function totalGamesPlayed(): int
    {
        return $this->playerGameStatistics()->where('is_present', true)->count();
    }

    /**
     * Total de golos
     */
    public function totalGoals(): int
    {
        return $this->playerGameStatistics()->sum('goals');
    }

    /**
     * Total de assistências
     */
    public function totalAssists(): int
    {
        return $this->playerGameStatistics()->sum('assists');
    }
}
