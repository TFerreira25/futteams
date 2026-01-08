<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\Player;
use App\Services\GameService;
use App\Services\RankingService;
use App\Services\ResultService;
use Illuminate\Console\Command;

class TestRankings extends Command
{
    protected $signature = 'test:rankings';

    protected $description = 'Test rankings system';

    public function handle(GameService $gameService, ResultService $resultService, RankingService $rankingService)
    {
        $this->info('📊 Testando sistema de rankings...');
        $this->info('');

        // Criar vários jogos com resultados variados
        $players = Player::active()->get();

        for ($gameNum = 1; $gameNum <= 3; $gameNum++) {
            $this->line('Criando jogo ' . $gameNum . '...');

            $playerIds = $players->random(8)->pluck('id')->toArray();
            $game = Game::create([
                'date' => now()->addDays($gameNum),
                'status' => 'pending',
            ]);

            try {
                $result = $gameService->generateTeams($game, $playerIds);
                $team1Players = $game->teamOne()->playerGameStatistics()->pluck('player_id')->toArray();
                $team2Players = $game->teamTwo()->playerGameStatistics()->pluck('player_id')->toArray();

                // Registar eventos aleatórios
                foreach ($team1Players as $index => $playerId) {
                    if (rand(0, 1)) {
                        $resultService->recordPlayerEvent($game, $playerId, 'goal', rand(1, 2));
                    }
                    if (rand(0, 2) === 0) {
                        $resultService->recordPlayerEvent($game, $playerId, 'assist', 1);
                    }
                }

                foreach ($team2Players as $index => $playerId) {
                    if (rand(0, 1)) {
                        $resultService->recordPlayerEvent($game, $playerId, 'goal', 1);
                    }
                    if (rand(0, 3) === 0) {
                        $resultService->recordPlayerEvent($game, $playerId, 'yellow_card', 1);
                    }
                }

                $resultService->recalculateTeamGoals($game);
                $team1Goals = $game->teamOne()->playerGameStatistics()->sum('goals');
                $team2Goals = $game->teamTwo()->playerGameStatistics()->sum('goals');
                $resultService->completeGame($game, $team1Goals, $team2Goals);

                $this->line('  ✅ Jogo ' . $gameNum . ' completado');
            } catch (\Exception $e) {
                $this->error('  ❌ Erro: ' . $e->getMessage());
            }
        }

        $this->info('');
        $this->info('<fg=cyan>TOP 5 - GOLOS POR JOGO</>');
        $ranking = $rankingService->getGoalsPerGameRanking(5);
        foreach ($ranking as $index => $player) {
            $this->line(($index + 1) . '. ' . $player['name'] . ' - ' . $player['goals_per_game'] . ' golos/jogo (' . $player['total_goals'] . ' em ' . $player['games_played'] . ')');
        }

        $this->info('');
        $this->info('<fg=cyan>TOP 5 - ASSISTÊNCIAS POR JOGO</>');
        $ranking = $rankingService->getAssistsPerGameRanking(5);
        foreach ($ranking as $index => $player) {
            $this->line(($index + 1) . '. ' . $player['name'] . ' - ' . $player['assists_per_game'] . ' assist./jogo (' . $player['total_assists'] . ' em ' . $player['games_played'] . ')');
        }

        $this->info('');
        $this->info('<fg=cyan>TOP 5 - PRESENÇAS</>');
        $ranking = $rankingService->getPresenceRanking(5);
        foreach ($ranking as $index => $player) {
            $this->line(($index + 1) . '. ' . $player['name'] . ' - ' . $player['games_played'] . ' jogos');
        }

        $this->info('');
        $this->info('✅ Teste de rankings concluído!');

        return 0;
    }
}
