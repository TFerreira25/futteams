<?php

namespace App\Repositories;

use App\Models\PlayerGameStatistic;

class StatisticRepository
{
    public function getByPlayerAndGame(int $playerId, int $gameId): ?PlayerGameStatistic
    {
        return PlayerGameStatistic::where('player_id', $playerId)
            ->where('game_id', $gameId)
            ->first();
    }

    public function update(PlayerGameStatistic $statistic, array $data): PlayerGameStatistic
    {
        $statistic->update($data);
        return $statistic->fresh();
    }

    public function increment(PlayerGameStatistic $statistic, string $field, int $amount = 1): PlayerGameStatistic
    {
        $statistic->increment($field, $amount);
        return $statistic->fresh();
    }

    public function getGameStatistics(int $gameId)
    {
        return PlayerGameStatistic::where('game_id', $gameId)
            ->with('player', 'position', 'team')
            ->get();
    }

    public function getPlayerStatistics(int $playerId)
    {
        return PlayerGameStatistic::where('player_id', $playerId)
            ->with('game', 'team', 'position')
            ->orderByDesc('created_at')
            ->get();
    }
}
