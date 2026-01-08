<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = ['date', 'status', 'total_players', 'notes'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'game_player')
            ->withPivot('present')
            ->withTimestamps();
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function team1()
    {
        return $this->hasOne(Team::class)->where('team_number', 1);
    }

    public function team2()
    {
        return $this->hasOne(Team::class)->where('team_number', 2);
    }

    public function playerGameStatistics(): HasMany
    {
        return $this->hasMany(PlayerGameStatistic::class);
    }

    /**
     * Retorna jogadores presentes
     */
    public function presentPlayers()
    {
        return $this->players()->wherePivot('present', true);
    }

    /**
     * Conta de jogadores presentes
     */
    public function countPresentPlayers(): int
    {
        return $this->presentPlayers()->count();
    }

    /**
     * Retorna Team 1
     */
    public function teamOne()
    {
        return $this->teams()->where('team_number', 1)->first();
    }

    /**
     * Retorna Team 2
     */
    public function teamTwo()
    {
        return $this->teams()->where('team_number', 2)->first();
    }
}
