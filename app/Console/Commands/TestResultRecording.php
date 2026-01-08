<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\Player;
use App\Services\GameService;
use App\Services\ResultService;
use Illuminate\Console\Command;

class TestResultRecording extends Command
{
    protected $signature = 'test:result-recording';

    protected $description = 'Test result recording and statistics';

    public function handle(GameService $gameService, ResultService $resultService)
    {
        $this->info('🏟️ Testando registo de resultados...');

        // 1. Gerar equipas
        $playerIds = Player::active()->pluck('id')->take(8)->toArray();
        $game = Game::create([
            'date' => now()->addDay(),
            'status' => 'pending',
        ]);

        $this->info('✅ Jogo criado: ID ' . $game->id);

        try {
            $result = $gameService->generateTeams($game, $playerIds);
            $this->info('✅ Equipas geradas');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar equipas: ' . $e->getMessage());
            return 1;
        }

        // 2. Registar eventos
        $this->info('');
        $this->info('<fg=blue>Registando Eventos...</>');

        $team1Players = $game->teamOne()->playerGameStatistics()->pluck('player_id')->toArray();
        $team2Players = $game->teamTwo()->playerGameStatistics()->pluck('player_id')->toArray();

        try {
            // Team 1 marca 3 golos
            for ($i = 0; $i < 3; $i++) {
                $player = $team1Players[$i] ?? $team1Players[0];
                $resultService->recordPlayerEvent($game, $player, 'goal', 1);
                $this->line('  ⚽ Jogador ' . $player . ' marcou golo');
            }

            // Team 2 marca 2 golos
            for ($i = 0; $i < 2; $i++) {
                $player = $team2Players[$i] ?? $team2Players[0];
                $resultService->recordPlayerEvent($game, $player, 'goal', 1);
                $this->line('  ⚽ Jogador ' . $player . ' marcou golo');
            }

            // Assistências
            $resultService->recordPlayerEvent($game, $team1Players[1] ?? $team1Players[0], 'assist', 1);
            $this->line('  🤝 Jogador ' . ($team1Players[1] ?? $team1Players[0]) . ' fez assistência');

            // Cartões
            $resultService->recordPlayerEvent($game, $team2Players[2] ?? $team2Players[0], 'yellow_card', 1);
            $this->line('  🟨 Jogador ' . ($team2Players[2] ?? $team2Players[0]) . ' levou cartão amarelo');

            // Recalcular golos das equipas
            $resultService->recalculateTeamGoals($game);
            $this->info('✅ Eventos registados com sucesso');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao registar evento: ' . $e->getMessage());
            return 1;
        }

        // 3. Completar jogo
        $this->info('');
        $this->info('<fg=green>Finalizando Jogo...</>');

        try {
            $completed = $resultService->completeGame($game, 3, 2, 'Teste automático');
            $this->info('✅ Jogo finalizado: Team 1 ' . $completed->teamOne()->goals . ' - ' . $completed->teamTwo()->goals . ' Team 2');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao completar jogo: ' . $e->getMessage());
            return 1;
        }

        // 4. Mostrar sumário
        $this->info('');
        $this->info('<fg=cyan>SUMÁRIO DO JOGO</>');

        $summary = $resultService->getGameSummary($game);

        $this->line('Team 1: ' . $summary['team1']['goals'] . ' golos');
        foreach ($summary['team1']['players'] as $player) {
            $stats = '🎽 ' . $player['player_name'] . ' (' . $player['position'] . ')';
            if ($player['goals'] > 0) {
                $stats .= ' ⚽ ' . $player['goals'] . ' golos';
            }
            if ($player['assists'] > 0) {
                $stats .= ' 🤝 ' . $player['assists'] . ' assist.';
            }
            if ($player['yellow_cards'] > 0) {
                $stats .= ' 🟨 ' . $player['yellow_cards'];
            }
            $this->line('  ' . $stats);
        }

        $this->info('');
        $this->line('Team 2: ' . $summary['team2']['goals'] . ' golos');
        foreach ($summary['team2']['players'] as $player) {
            $stats = '🎽 ' . $player['player_name'] . ' (' . $player['position'] . ')';
            if ($player['goals'] > 0) {
                $stats .= ' ⚽ ' . $player['goals'] . ' golos';
            }
            if ($player['assists'] > 0) {
                $stats .= ' 🤝 ' . $player['assists'] . ' assist.';
            }
            if ($player['yellow_cards'] > 0) {
                $stats .= ' 🟨 ' . $player['yellow_cards'];
            }
            if ($player['red_cards'] > 0) {
                $stats .= ' 🟥 ' . $player['red_cards'];
            }
            $this->line('  ' . $stats);
        }

        $this->info('');
        $this->info('✅ Teste concluído com sucesso!');

        return 0;
    }
}
