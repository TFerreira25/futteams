<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameEvent;
use App\Models\PlayerGameStatistic;
use App\Repositories\StatisticRepository;
use Exception;

class ResultService
{
    public function __construct(private StatisticRepository $statisticRepository) {}

    public function recordPlayerEvent(Game $game, int $playerId, string $eventType, int $quantity = 1): PlayerGameStatistic
    {
        $statistic = $this->statisticRepository->getByPlayerAndGame($playerId, $game->id);

        if (!$statistic) {
            throw new Exception('Jogador não encontrado neste jogo');
        }

        match ($eventType) {
            'goal' => $this->statisticRepository->increment($statistic, 'goals', $quantity),
            'assist' => $this->statisticRepository->increment($statistic, 'assists', $quantity),
            'yellow_card' => $this->statisticRepository->increment($statistic, 'yellow_cards', $quantity),
            'red_card' => $this->statisticRepository->increment($statistic, 'red_cards', $quantity),
            'goals_conceded' => $this->statisticRepository->increment($statistic, 'goals_conceded', $quantity),
            default => throw new Exception('Tipo de evento inválido: ' . $eventType),
        };

        GameEvent::create([
            'game_id' => $game->id,
            'team_id' => $statistic->team_id,
            'player_id' => $playerId,
            'event_type' => $eventType,
            'quantity' => $quantity,
        ]);

        return $statistic->fresh();
    }

    public function recalculateTeamGoals(Game $game): void
    {
        $team1 = $game->teamOne();
        $team2 = $game->teamTwo();

        if ($team1) {
            $team1GoalsTotal = $team1->playerGameStatistics()->sum('goals');
            $team1->update(['goals' => $team1GoalsTotal]);
        }

        if ($team2) {
            $team2GoalsTotal = $team2->playerGameStatistics()->sum('goals');
            $team2->update(['goals' => $team2GoalsTotal]);
        }
    }

    public function completeGame(Game $game, int $team1Goals, int $team2Goals, ?string $notes = null): Game
    {
        $team1 = $game->teamOne();
        $team2 = $game->teamTwo();

        if (!$team1 || !$team2) {
            throw new Exception('Equipas não foram geradas para este jogo');
        }

        $team1->update([
            'goals' => $team1Goals,
            'goals_against' => $team2Goals,
        ]);

        $team2->update([
            'goals' => $team2Goals,
            'goals_against' => $team1Goals,
        ]);

        $game->update([
            'status' => 'completed',
            'notes' => $notes,
        ]);

        return $game->fresh();
    }

    public function getGameSummary(Game $game): array
    {
        $team1 = $game->teamOne();
        $team2 = $game->teamTwo();

        $team1Stats = [];
        $team2Stats = [];

        if ($team1) {
            $team1Stats = $team1->playerGameStatistics()
                ->with('player', 'position')
                ->get()
                ->map(fn($stat) => [
                    'player_id' => $stat->player->id,
                    'player_name' => $stat->player->name,
                    'position' => $stat->position->code,
                    'goals' => $stat->goals,
                    'assists' => $stat->assists,
                    'yellow_cards' => $stat->yellow_cards,
                    'red_cards' => $stat->red_cards,
                    'goals_conceded' => $stat->goals_conceded,
                ])
                ->toArray();
        }

        if ($team2) {
            $team2Stats = $team2->playerGameStatistics()
                ->with('player', 'position')
                ->get()
                ->map(fn($stat) => [
                    'player_id' => $stat->player->id,
                    'player_name' => $stat->player->name,
                    'position' => $stat->position->code,
                    'goals' => $stat->goals,
                    'assists' => $stat->assists,
                    'yellow_cards' => $stat->yellow_cards,
                    'red_cards' => $stat->red_cards,
                    'goals_conceded' => $stat->goals_conceded,
                ])
                ->toArray();
        }

        return [
            'game_id' => $game->id,
            'game_date' => $game->date,
            'game_status' => $game->status,
            'team1' => [
                'id' => $team1->id,
                'goals' => $team1->goals,
                'goals_against' => $team1->goals_against,
                'players' => $team1Stats,
            ],
            'team2' => [
                'id' => $team2->id,
                'goals' => $team2->goals,
                'goals_against' => $team2->goals_against,
                'players' => $team2Stats,
            ],
        ];
    }

    public function undoPlayerEvent(Game $game, int $playerId, string $eventType, int $quantity = 1): PlayerGameStatistic
    {
        $statistic = $this->statisticRepository->getByPlayerAndGame($playerId, $game->id);

        if (!$statistic) {
            throw new Exception('Jogador não encontrado neste jogo');
        }

        match ($eventType) {
            'goal' => $statistic->decrement('goals', $quantity),
            'assist' => $statistic->decrement('assists', $quantity),
            'yellow_card' => $statistic->decrement('yellow_cards', $quantity),
            'red_card' => $statistic->decrement('red_cards', $quantity),
            'goals_conceded' => $statistic->decrement('goals_conceded', $quantity),
            default => throw new Exception('Tipo de evento inválido: ' . $eventType),
        };

        $event = GameEvent::where('game_id', $game->id)
            ->where('player_id', $playerId)
            ->where('event_type', $eventType)
            ->orderByDesc('id')
            ->first();
        if ($event) {
            $event->delete();
        }

        return $statistic->fresh();
    }

    public function getGameEvents(Game $game): array
    {
        return GameEvent::with(['player', 'team'])
            ->where('game_id', $game->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($evt) {
                return [
                    'player_id' => $evt->player_id,
                    'player_name' => $evt->player?->name,
                    'event_type' => $evt->event_type,
                    'team' => $evt->team?->team_number,
                    'quantity' => $evt->quantity,
                    'created_at' => $evt->created_at,
                ];
            })
            ->toArray();
    }
}
