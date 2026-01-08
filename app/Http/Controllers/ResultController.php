<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteGameRequest;
use App\Http\Requests\RecordPlayerEventRequest;
use App\Models\Game;
use App\Services\GameService;
use App\Services\ResultService;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function __construct(
        private ResultService $resultService,
        private GameService $gameService
    ) {}

    public function recordEvent(Game $game, RecordPlayerEventRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $statistic = $this->resultService->recordPlayerEvent(
                $game,
                $data['player_id'],
                $data['event_type'],
                $data['quantity']
            );

            $this->resultService->recalculateTeamGoals($game);

            return response()->json([
                'success' => true,
                'message' => 'Evento registado com sucesso',
                'statistic' => [
                    'player_id' => $statistic->player_id,
                    'goals' => $statistic->goals,
                    'assists' => $statistic->assists,
                    'yellow_cards' => $statistic->yellow_cards,
                    'red_cards' => $statistic->red_cards,
                    'goals_conceded' => $statistic->goals_conceded,
                ],
                'game' => $this->gameService->formatForFrontend($game),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function undoEvent(Game $game, RecordPlayerEventRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $statistic = $this->resultService->undoPlayerEvent(
                $game,
                $data['player_id'],
                $data['event_type'],
                $data['quantity']
            );

            $this->resultService->recalculateTeamGoals($game);

            return response()->json([
                'success' => true,
                'message' => 'Evento desfeito com sucesso',
                'statistic' => [
                    'player_id' => $statistic->player_id,
                    'goals' => $statistic->goals,
                    'assists' => $statistic->assists,
                    'yellow_cards' => $statistic->yellow_cards,
                    'red_cards' => $statistic->red_cards,
                    'goals_conceded' => $statistic->goals_conceded,
                ],
                'game' => $this->gameService->formatForFrontend($game),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function completeGame(Game $game, CompleteGameRequest $request)
    {
        try {
            $data = $request->validated();

            $completed = $this->resultService->completeGame(
                $game,
                $data['team1_goals'],
                $data['team2_goals'],
                $data['notes'] ?? null
            );

            return redirect()
                ->route('games.show', $completed->id)
                ->with('success', 'Jogo finalizado com sucesso');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function getSummary(Game $game): JsonResponse
    {
        $summary = $this->resultService->getGameSummary($game);

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    public function apiGetGameStatistics(Game $game): JsonResponse
    {
        $summary = $this->resultService->getGameSummary($game);

        return response()->json($summary);
    }

    public function apiGetGameEvents(Game $game): JsonResponse
    {
        $events = $this->resultService->getGameEvents($game);

        return response()->json($events);
    }
}
