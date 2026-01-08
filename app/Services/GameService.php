<?php

namespace App\Services;

use App\Models\Game;
use App\Repositories\GameRepository;

class GameService
{
    public function __construct(
        private GameRepository $gameRepository,
        private TeamGenerationService $teamGenerationService
    ) {}

    public function create(array $data): Game
    {
        return $this->gameRepository->create($data);
    }

    public function update(Game $game, array $data): Game
    {
        return $this->gameRepository->update($game, $data);
    }

    public function delete(Game $game): bool
    {
        return $this->gameRepository->delete($game);
    }

    public function getById(int $id): ?Game
    {
        return $this->gameRepository->getById($id);
    }

    public function getAll()
    {
        return $this->gameRepository->getAll();
    }

    public function generateTeams(Game $game, array $playerIds)
    {
        return $this->teamGenerationService->generateTeams($game, $playerIds);
    }

    public function formatForFrontend(Game $game): array
    {
        $team1 = $game->teamOne();
        $team2 = $game->teamTwo();

        return [
            'id' => $game->id,
            'date' => $game->date,
            'status' => $game->status,
            'total_players' => $game->total_players,
            'notes' => $game->notes,
            'team1' => $team1 ? $this->formatTeam($team1) : null,
            'team2' => $team2 ? $this->formatTeam($team2) : null,
        ];
    }

    private function formatTeam($team): array
    {
        $stats = $team->playerGameStatistics()->with('player', 'position')->get();

        return [
            'id' => $team->id,
            'game_id' => $team->game_id,
            'team_number' => $team->team_number,
            'goals' => $team->goals,
            'goals_against' => $team->goals_against,
            'players' => $stats->map(fn($stat) => [
                'id' => $stat->player->id,
                'name' => $stat->player->name,
                'position' => $stat->position->name,
                'position_code' => $stat->position->code,
                'goals' => $stat->goals,
                'assists' => $stat->assists,
                'cards' => $stat->yellow_cards . 'Y/' . $stat->red_cards . 'R',
            ]),
        ];
    }
}
