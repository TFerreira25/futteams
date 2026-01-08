<?php

namespace App\Repositories;

use App\Models\Team;

class TeamRepository
{
    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);
        return $team->fresh();
    }

    public function getByGameAndNumber(int $gameId, int $teamNumber): ?Team
    {
        return Team::where('game_id', $gameId)
            ->where('team_number', $teamNumber)
            ->with('playerGameStatistics.player', 'playerGameStatistics.position')
            ->first();
    }
}
