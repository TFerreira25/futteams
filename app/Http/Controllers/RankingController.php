<?php

namespace App\Http\Controllers;

use App\Services\RankingService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function __construct(private RankingService $rankingService) {}

    public function dashboard(): Response
    {
        $dashboard = $this->rankingService->getDashboard(10);

        return Inertia::render('dashboard', [
            'topGoalsPerGame' => $dashboard['top_goals_per_game'],
            'topAssistsPerGame' => $dashboard['top_assists_per_game'],
            'topTotalGoals' => $dashboard['top_total_goals'],
            'topPresence' => $dashboard['top_presence'],
            'recentGames' => $dashboard['recent_games'] ?? [],
        ]);
    }

    public function index(): Response
    {
        $dashboard = $this->rankingService->getDashboard(10);

        return Inertia::render('Rankings/Index', [
            'topGoalsPerGame' => $dashboard['top_goals_per_game'],
            'topAssistsPerGame' => $dashboard['top_assists_per_game'],
            'topTotalGoals' => $dashboard['top_total_goals'],
            'topPresence' => $dashboard['top_presence'],
        ]);
    }

    public function goalsPerGame(): Response
    {
        $ranking = $this->rankingService->getGoalsPerGameRanking(50);

        return Inertia::render('Rankings/GoalsPerGame', [
            'ranking' => $ranking,
            'title' => 'Golos por Jogo',
        ]);
    }

    public function assistsPerGame(): Response
    {
        $ranking = $this->rankingService->getAssistsPerGameRanking(50);

        return Inertia::render('Rankings/AssistsPerGame', [
            'ranking' => $ranking,
            'title' => 'Assistências por Jogo',
        ]);
    }

    public function totalGoals(): Response
    {
        $ranking = $this->rankingService->getTotalGoalsRanking(50);

        return Inertia::render('Rankings/TotalGoals', [
            'ranking' => $ranking,
            'title' => 'Total de Golos',
        ]);
    }

    public function presence(): Response
    {
        $ranking = $this->rankingService->getPresenceRanking(50);

        return Inertia::render('Rankings/Presence', [
            'ranking' => $ranking,
            'title' => 'Jogos Disputados',
        ]);
    }

    public function byPosition(string $position): Response
    {
        $ranking = $this->rankingService->getRankingByPosition(strtoupper($position), 50);

        return Inertia::render('Rankings/ByPosition', [
            'ranking' => $ranking,
            'position' => strtoupper($position),
        ]);
    }

    public function apiGoalsPerGame(int $limit = 20): JsonResponse
    {
        return response()->json($this->rankingService->getGoalsPerGameRanking($limit));
    }

    public function apiAssistsPerGame(int $limit = 20): JsonResponse
    {
        return response()->json($this->rankingService->getAssistsPerGameRanking($limit));
    }

    public function apiTotalGoals(int $limit = 20): JsonResponse
    {
        return response()->json($this->rankingService->getTotalGoalsRanking($limit));
    }

    public function apiPresence(int $limit = 20): JsonResponse
    {
        return response()->json($this->rankingService->getPresenceRanking($limit));
    }

    public function apiByPosition(string $position, int $limit = 20): JsonResponse
    {
        return response()->json($this->rankingService->getRankingByPosition(strtoupper($position), $limit));
    }

    public function apiDashboard(): JsonResponse
    {
        return response()->json($this->rankingService->getDashboard(10));
    }

    public function apiCompare(int $player1Id, int $player2Id): JsonResponse
    {
        $comparison = $this->rankingService->comparePlayersStats($player1Id, $player2Id);

        return response()->json($comparison);
    }
}
