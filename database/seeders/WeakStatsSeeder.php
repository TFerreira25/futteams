<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStatistic;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Database\Seeder;

class WeakStatsSeeder extends Seeder
{
    public function run(): void
    {
        // Garantir que as posições existem
        $this->call(PositionSeeder::class);

        // Obter posições
        $gk = Position::where('code', 'GK')->first();
        $def = Position::where('code', 'DEF')->first();
        $mid = Position::where('code', 'MID')->first();
        $fwd = Position::where('code', 'FWD')->first();

        // Criar jogadores com estatísticas fracas
        $weakPlayers = [
            // Guarda-redes fracos
            ['name' => 'Fábio Fraco', 'email' => 'fabio.fraco@example.com', 'position_id' => $gk->id],
            ['name' => 'Rui Inexperiente', 'email' => 'rui.inexperiente@example.com', 'position_id' => $gk->id],

            // Defesas fracos
            ['name' => 'Zé Mau', 'email' => 'ze.mau@example.com', 'position_id' => $def->id],
            ['name' => 'Nando Confuso', 'email' => 'nando.confuso@example.com', 'position_id' => $def->id],
            ['name' => 'José Falhador', 'email' => 'jose.falhador@example.com', 'position_id' => $def->id],

            // Médios fracos
            ['name' => 'Paulo Lento', 'email' => 'paulo.lento@example.com', 'position_id' => $mid->id],
            ['name' => 'Marcelo Inseguro', 'email' => 'marcelo.inseguro@example.com', 'position_id' => $mid->id],
            ['name' => 'Vítor Tremendo', 'email' => 'vitor.tremendo@example.com', 'position_id' => $mid->id],

            // Avançados fracos
            ['name' => 'Dário Desastrado', 'email' => 'dario.desastrado@example.com', 'position_id' => $fwd->id],
            ['name' => 'Rodrigo Impreciso', 'email' => 'rodrigo.impreciso@example.com', 'position_id' => $fwd->id],
        ];

        $players = [];
        foreach ($weakPlayers as $playerData) {
            $players[] = Player::create($playerData);
        }

        // Criar apenas 3 jogos com estatísticas muito fracas
        $games = [];
        for ($i = 0; $i < 3; $i++) {
            $game = Game::create([
                'date' => now()->subDays(rand(5, 30)),
                'status' => 'completed',
            ]);
            $games[] = $game;

            // Distribuir jogadores aleatoriamente entre os 2 times
            shuffle($players);
            $team1 = array_slice($players, 0, 5);
            $team2 = array_slice($players, 5, 5);

            // Criar teams do jogo
            $teamRecord1 = Team::create([
                'game_id' => $game->id,
                'team_number' => 1,
            ]);
            $teamRecord2 = Team::create([
                'game_id' => $game->id,
                'team_number' => 2,
            ]);

            // Estatísticas muito fracas
            foreach ($players as $player) {
                $isInTeam1 = in_array($player->id, collect($team1)->pluck('id')->toArray());
                $isInTeam2 = in_array($player->id, collect($team2)->pluck('id')->toArray());

                if ($isInTeam1 || $isInTeam2) {
                    $teamRecord = $isInTeam1 ? $teamRecord1 : $teamRecord2;
                    PlayerGameStatistic::create([
                        'game_id' => $game->id,
                        'player_id' => $player->id,
                        'team_id' => $teamRecord->id,
                        'position_id' => $player->position_id,
                        'goals' => rand(0, 1), // 0 ou 1 golo máximo
                        'assists' => 0, // Sem assistências
                        'is_present' => true,
                    ]);
                }
            }
        }
    }
}
