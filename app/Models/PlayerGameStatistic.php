<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerGameStatistic extends Model
{
    protected $fillable = [
        'player_id',
        'game_id',
        'team_id',
        'position_id',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'goals_conceded',
        'is_present',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
