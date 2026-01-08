<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStatistic;
use App\Models\Position;
use App\Models\Team;
use App\Repositories\GameRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use Exception;

class TeamGenerationService
{
    public function __construct(
        private GameRepository $gameRepository,
        private PlayerRepository $playerRepository,
        private TeamRepository $teamRepository
    ) {}

    public function generateTeams(Game $game, array $playerIds): array
    {
        $validation = $this->validateTeamGeneration($playerIds);
        if ($validation['errors']) {
            throw new Exception(implode(', ', $validation['errors']));
        }

        $players = Player::whereIn('id', $playerIds)
            ->with('position')
            ->get();

        $playersWithMetrics = $this->calculatePlayerMetrics($players);

        $playersByPosition = $this->groupByPosition($playersWithMetrics);

        $compositionValidation = $this->validatePositionComposition($playersByPosition);
        if ($compositionValidation['errors']) {
            throw new Exception('Composição de posições inválida: ' . implode(', ', $compositionValidation['errors']));
        }

        $teamAssignments = $this->distributePlayersToTeams($playersWithMetrics, $playersByPosition);

        $team1 = $this->createTeamWithPlayers($game, 1, $teamAssignments['team1']);
        $team2 = $this->createTeamWithPlayers($game, 2, $teamAssignments['team2']);

        $this->gameRepository->update($game, [
            'status' => 'team_generation',
            'total_players' => count($playerIds),
        ]);

        return [
            'team1' => $team1,
            'team2' => $team2,
            'validation' => $validation,
        ];
    }

    private function validateTeamGeneration(array $playerIds): array
    {
        $errors = [];
        $warnings = [];

        $total = count($playerIds);

        // Limite máximo e mínimo (máx 18, min 4)
        if ($total < 4) {
            $errors[] = 'Mínimo de 4 jogadores (2 por equipa)';
        }

        if ($total > 18) {
            $errors[] = 'Máximo de 18 jogadores (atualmente: ' . $total . ')';
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'valid' => empty($errors),
        ];
    }

    private function calculatePlayerMetrics($players)
    {
        return $players->map(function (Player $player) {
            $gamesPlayed = $player->totalGamesPlayed();
            $totalGoals = $player->totalGoals();

            if ($gamesPlayed >= 3) {
                $ratio = $player->goalsPerGame();
            } else {
                $ratio = $gamesPlayed > 0 ? ($totalGoals / 2) : 0;
            }

            return [
                'player' => $player,
                'position_id' => $player->position_id,
                'position_code' => $player->position->code,
                'games_played' => $gamesPlayed,
                'total_goals' => $totalGoals,
                'ratio' => $ratio,
            ];
        })->toArray();
    }

    private function groupByPosition($playersWithMetrics): array
    {
        $grouped = [];

        foreach ($playersWithMetrics as $data) {
            $posCode = $data['position_code'];
            if (!isset($grouped[$posCode])) {
                $grouped[$posCode] = [];
            }
            $grouped[$posCode][] = $data;
        }

        foreach ($grouped as &$group) {
            usort($group, fn($a, $b) => $b['ratio'] <=> $a['ratio']);
        }

        return $grouped;
    }

    private function validatePositionComposition($playersByPosition): array
    {
        $errors = [];

        $gkCount = count($playersByPosition['GK'] ?? []);
        if ($gkCount < 2) {
            $errors[] = 'Mínimo de 2 guarda-redes (atualmente: ' . $gkCount . ')';
        }

        return [
            'errors' => $errors,
            'valid' => empty($errors),
        ];
    }

    private function distributePlayersToTeams($playersWithMetrics, $playersByPosition): array
    {
        $team1 = [];
        $team2 = [];

        $gks = $playersByPosition['GK'];
        foreach ($gks as $index => $gkData) {
            if ($index % 2 === 0) {
                $team1[] = $gkData;
            } else {
                $team2[] = $gkData;
            }
        }

        foreach (['DEF', 'MID', 'FWD'] as $position) {
            if (isset($playersByPosition[$position])) {
                $posPlayers = $playersByPosition[$position];

                foreach ($posPlayers as $index => $playerData) {
                    if ($index % 2 === 0) {
                        $team1[] = $playerData;
                    } else {
                        $team2[] = $playerData;
                    }
                }
            }
        }

        return [
            'team1' => $team1,
            'team2' => $team2,
        ];
    }

    private function createTeamWithPlayers(Game $game, int $teamNumber, array $playersData): Team
    {
        $team = $this->teamRepository->create([
            'game_id' => $game->id,
            'team_number' => $teamNumber,
        ]);

        foreach ($playersData as $playerData) {
            PlayerGameStatistic::create([
                'player_id' => $playerData['player']->id,
                'game_id' => $game->id,
                'team_id' => $team->id,
                'position_id' => $playerData['position_id'],
                'is_present' => true,
            ]);
        }

        return $team->fresh();
    }
}
