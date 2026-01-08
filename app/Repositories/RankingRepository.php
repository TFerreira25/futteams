<?php

namespace App\Repositories;

use App\Models\Player;
use Illuminate\Support\Collection;

class RankingRepository
{
    public function getGoalsPerGameRanking(int $limit = 20): Collection
    {
        return Player::active()
            ->with('position')
            ->get()
            ->filter(fn($player) => $player->totalGamesPlayed() > 0)
            ->map(fn($player) => [
                'player_id' => $player->id,
                'name' => $player->name,
                'position' => $player->position->name,
                'position_code' => $player->position->code,
                'games_played' => $player->totalGamesPlayed(),
                'total_goals' => $player->totalGoals(),
                'goals_per_game' => $player->goalsPerGame(),
            ])
            ->sortByDesc('goals_per_game')
            ->take($limit)
            ->values();
    }

    public function getAssistsPerGameRanking(int $limit = 20): Collection
    {
        return Player::active()
            ->with('position')
            ->get()
            ->filter(fn($player) => $player->totalGamesPlayed() > 0)
            ->map(fn($player) => [
                'player_id' => $player->id,
                'name' => $player->name,
                'position' => $player->position->name,
                'position_code' => $player->position->code,
                'games_played' => $player->totalGamesPlayed(),
                'total_assists' => $player->totalAssists(),
                'assists_per_game' => $player->assistsPerGame(),
            ])
            ->sortByDesc('assists_per_game')
            ->take($limit)
            ->values();
    }

    public function getTotalGoalsRanking(int $limit = 20): Collection
    {
        return Player::active()
            ->with('position')
            ->get()
            ->map(fn($player) => [
                'player_id' => $player->id,
                'name' => $player->name,
                'position' => $player->position->name,
                'position_code' => $player->position->code,
                'games_played' => $player->totalGamesPlayed(),
                'total_goals' => $player->totalGoals(),
            ])
            ->sortByDesc('total_goals')
            ->take($limit)
            ->values();
    }

    public function getPresenceRanking(int $limit = 20): Collection
    {
        return Player::active()
            ->with('position')
            ->get()
            ->map(fn($player) => [
                'player_id' => $player->id,
                'name' => $player->name,
                'position' => $player->position->name,
                'position_code' => $player->position->code,
                'games_played' => $player->totalGamesPlayed(),
            ])
            ->sortByDesc('games_played')
            ->take($limit)
            ->values();
    }

    public function getRankingByPosition(string $positionCode, int $limit = 20): Collection
    {
        return Player::active()
            ->with('position')
            ->get()
            ->filter(fn($player) => $player->position->code === $positionCode)
            ->filter(fn($player) => $player->totalGamesPlayed() > 0)
            ->map(fn($player) => [
                'player_id' => $player->id,
                'name' => $player->name,
                'position' => $player->position->name,
                'games_played' => $player->totalGamesPlayed(),
                'total_goals' => $player->totalGoals(),
                'total_assists' => $player->totalAssists(),
                'goals_per_game' => $player->goalsPerGame(),
                'assists_per_game' => $player->assistsPerGame(),
            ])
            ->sortByDesc('goals_per_game')
            ->take($limit)
            ->values();
    }

    public function comparePlayersStats(int $player1Id, int $player2Id): array
    {
        $player1 = Player::find($player1Id);
        $player2 = Player::find($player2Id);

        if (!$player1 || !$player2) {
            return [];
        }

        return [
            'player1' => [
                'id' => $player1->id,
                'name' => $player1->name,
                'position' => $player1->position->name,
                'games_played' => $player1->totalGamesPlayed(),
                'total_goals' => $player1->totalGoals(),
                'total_assists' => $player1->totalAssists(),
                'goals_per_game' => $player1->goalsPerGame(),
                'assists_per_game' => $player1->assistsPerGame(),
            ],
            'player2' => [
                'id' => $player2->id,
                'name' => $player2->name,
                'position' => $player2->position->name,
                'games_played' => $player2->totalGamesPlayed(),
                'total_goals' => $player2->totalGoals(),
                'total_assists' => $player2->totalAssists(),
                'goals_per_game' => $player2->goalsPerGame(),
                'assists_per_game' => $player2->assistsPerGame(),
            ],
        ];
    }
}
