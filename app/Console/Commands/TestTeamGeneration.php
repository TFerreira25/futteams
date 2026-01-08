<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\Player;
use App\Services\GameService;
use Illuminate\Console\Command;

class TestTeamGeneration extends Command
{
    protected $signature = 'test:team-generation';

    protected $description = 'Test team generation algorithm';

    public function handle(GameService $gameService)
    {
        $this->info('🏟️ Testando algoritmo de geração de equipas...');

        // Obter 8 jogadores ativos
        $playerIds = Player::active()->pluck('id')->take(8)->toArray();

        if (count($playerIds) < 8) {
            $this->error('❌ Necessário pelo menos 8 jogadores ativos no banco');
            return 1;
        }

        $this->info('✅ Encontrados ' . count($playerIds) . ' jogadores');

        // Criar jogo
        $game = Game::create([
            'date' => now()->addDay(),
            'status' => 'pending',
        ]);
        $this->info('✅ Jogo criado: ID ' . $game->id);

        // Gerar equipas
        try {
            $result = $gameService->generateTeams($game, $playerIds);

            $this->info('✅ Equipas geradas com sucesso!');
            $this->info('');

            // Mostrar Team 1
            $this->line('<fg=blue>TEAM 1</>');
            $team1 = $result['team1']->playerGameStatistics()->with('player', 'position')->get();
            $total1 = 0;
            foreach ($team1 as $stat) {
                $this->line('  ' . $stat->player->name . ' (' . $stat->position->code . ')');
                $total1++;
            }
            $this->line('  Total: ' . $total1);
            $this->info('');

            // Mostrar Team 2
            $this->line('<fg=green>TEAM 2</>');
            $team2 = $result['team2']->playerGameStatistics()->with('player', 'position')->get();
            $total2 = 0;
            foreach ($team2 as $stat) {
                $this->line('  ' . $stat->player->name . ' (' . $stat->position->code . ')');
                $total2++;
            }
            $this->line('  Total: ' . $total2);
            $this->info('');

            $this->info('Validation: ' . json_encode($result['validation']));

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            return 1;
        }
    }
}
