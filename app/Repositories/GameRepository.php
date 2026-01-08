<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;

class GameRepository
{
    public function getAll(): Collection
    {
        return Game::with(['teams.playerGameStatistics.player', 'teams.playerGameStatistics.position'])
            ->orderByDesc('date')
            ->get();
    }

    public function getById(int $id): ?Game
    {
        return Game::with(['teams.playerGameStatistics.player', 'teams.playerGameStatistics.position'])->find($id);
    }

    public function create(array $data): Game
    {
        return Game::create($data);
    }

    public function update(Game $game, array $data): Game
    {
        $game->update($data);
        return $game->fresh();
    }

    public function delete(Game $game): bool
    {
        return $game->delete();
    }

    public function getRecent(int $limit = 10): Collection
    {
        return Game::orderByDesc('date')
            ->limit($limit)
            ->get();
    }

    public function getCompleted(): Collection
    {
        return Game::where('status', 'completed')
            ->orderByDesc('date')
            ->get();
    }
}
