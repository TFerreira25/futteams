<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = ['code', 'name', 'max_per_team', 'sort_order'];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function playerGameStatistics(): HasMany
    {
        return $this->hasMany(PlayerGameStatistic::class);
    }
}
