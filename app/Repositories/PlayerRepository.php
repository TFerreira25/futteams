<?php

namespace App\Repositories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Collection;

class PlayerRepository
{
    public function getActive(): Collection
    {
        return Player::where('active', true)
            ->with('position')
            ->orderBy('name')
            ->get();
    }

    public function getAll(): Collection
    {
        return Player::with('position')
            ->orderBy('name')
            ->get();
    }

    public function getById(int $id): ?Player
    {
        return Player::with('position')->find($id);
    }

    public function getByPosition(int $positionId): Collection
    {
        return Player::where('position_id', $positionId)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Player
    {
        return Player::create($data);
    }

    public function update(Player $player, array $data): Player
    {
        $player->update($data);
        return $player->fresh();
    }

    public function delete(Player $player): bool
    {
        return $player->delete();
    }

    public function getPlayerStats(Player $player): array
    {
        return [
            'id' => $player->id,
            'name' => $player->name,
            'position' => $player->position->name,
            'total_games' => $player->totalGamesPlayed(),
            'total_goals' => $player->totalGoals(),
            'goals_per_game' => $player->goalsPerGame(),
            'total_assists' => $player->totalAssists(),
            'assists_per_game' => $player->assistsPerGame(),
        ];
    }
}
