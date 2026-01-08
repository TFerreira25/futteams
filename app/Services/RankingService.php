<?php

namespace App\Services;

use App\Repositories\RankingRepository;

class RankingService
{
    public function __construct(private RankingRepository $rankingRepository) {}

    public function getGoalsPerGameRanking(int $limit = 20)
    {
        return $this->rankingRepository->getGoalsPerGameRanking($limit);
    }

    public function getAssistsPerGameRanking(int $limit = 20)
    {
        return $this->rankingRepository->getAssistsPerGameRanking($limit);
    }

    public function getTotalGoalsRanking(int $limit = 20)
    {
        return $this->rankingRepository->getTotalGoalsRanking($limit);
    }

    public function getPresenceRanking(int $limit = 20)
    {
        return $this->rankingRepository->getPresenceRanking($limit);
    }

    public function getRankingByPosition(string $positionCode, int $limit = 20)
    {
        return $this->rankingRepository->getRankingByPosition($positionCode, $limit);
    }

    public function comparePlayersStats(int $player1Id, int $player2Id)
    {
        return $this->rankingRepository->comparePlayersStats($player1Id, $player2Id);
    }

    public function getDashboard(int $limit = 10): array
    {
        $recentGames = \App\Models\Game::orderBy('date', 'desc')
            ->with(['teams', 'playerGameStatistics.player', 'playerGameStatistics.position'])
            ->limit(5)
            ->get();

        return [
            'top_goals_per_game' => $this->getGoalsPerGameRanking($limit),
            'top_assists_per_game' => $this->getAssistsPerGameRanking($limit),
            'top_total_goals' => $this->getTotalGoalsRanking($limit),
            'top_presence' => $this->getPresenceRanking($limit),
            'recent_games' => $recentGames,
        ];
    }
}
