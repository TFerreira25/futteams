<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerGameStatistic;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Database\Seeder;

class StrongStatsSeeder extends Seeder
{
    private $allGames = [];

    public function run(): void
    {
        // Garantir que as posições existem
        $this->call(PositionSeeder::class);

        // Obter posições
        $gk = Position::where('code', 'GK')->first();
        $def = Position::where('code', 'DEF')->first();
        $mid = Position::where('code', 'MID')->first();
        $fwd = Position::where('code', 'FWD')->first();

        // Nomes de jogadores
        $playerNames = [
            'Ederson',
            'Alisson',
            'De Gea',
            'Courtois',
            'Neuer',
            'Oblak',
            'Donnarumma',
            'Ter Stegen',
            'Reina',
            'Lloris',
            'Ramsdale',
            'Pickford',
            'Pope',
            'Henderson',
            'Forster',
            'Ward',
            'Fabianski',
            'Patrício',
            'Gunn',
            'Bazunu',
            'Van Dijk',
            'Dias',
            'Hummels',
            'Rüdiger',
            'Varane',
            'Kimpembe',
            'Marquinhos',
            'Silva',
            'Akanji',
            'De Vrij',
            'Sánchez',
            'Otamendi',
            'Tuanzebe',
            'Lindelöf',
            'Maguire',
            'Shaw',
            'Telles',
            'Malacia',
            'Dalot',
            'Wan-Bissaka',
            'Porro',
            'Castagne',
            'Perisic',
            'Reguilón',
            'Carvajal',
            'Nacho',
            'Bellerin',
            'Hernández',
            'Mendy',
            'Cancelo',
            'De Bruyne',
            'Rodri',
            'Gündogan',
            'Álvarez',
            'Grealish',
            'Mahrez',
            'Foden',
            'Fernandes',
            'Eriksen',
            'Casemiro',
            'McTominay',
            'Veretout',
            'Locatelli',
            'Fabian',
            'Barrenechea',
            'Koopmeiners',
            'Miranchuk',
            'Gavi',
            'Pedri',
            'De Jong',
            'Busquets',
            'Iniesta',
            'Xavi',
            'Puyol',
            'Rakitic',
            'Suárez',
            'Messi',
            'Neymar',
            'Mbappé',
            'Cavani',
            'Benzema',
            'Ronaldo',
            'Lewandowski',
            'Haaland',
            'Kane',
            'Vardy',
            'Antony',
            'Sancho',
            'Rashford',
            'Martial',
            'Pereira',
            'Rocha',
            'Monteiro',
            'Almeida',
            'Ferreira',
            'Lopes',
            'Ribeiro',
            'Carvalho',
            'Gomes',
            'Martins',
            'Freitas',
            'Neves',
            'Moutinho',
            'João',
            'Sérgio',
            'Hugo',
            'João Félix',
            'Trincão',
            'Leão',
            'Baleba',
            'Ndiaye',
            'Aarons',
            'Rooney',
            'Anderson',
            'Giggs',
            'Beckham',
            'Scholes',
            'Gerrard',
            'Lampard',
            'Vieira',
            'Senna',
            'Zidane',
            'Silva',
            'Fernández',
            'González',
            'López',
            'Martínez',
            'García',
            'Sánchez',
            'Torres',
        ];

        // Criar 40 jogos primeiro
        for ($gameNum = 0; $gameNum < 40; $gameNum++) {
            $game = Game::create([
                'date' => now()->subDays(rand(5, 180)),
                'status' => 'completed',
            ]);

            // Criar teams do jogo
            Team::create(['game_id' => $game->id, 'team_number' => 1]);
            Team::create(['game_id' => $game->id, 'team_number' => 2]);

            $this->allGames[] = $game;
        }

        $players = [];
        $positions = [$gk, $def, $mid, $fwd];
        $posIndex = 0;

        // Distribuir 141 jogadores
        for ($i = 0; $i < 141; $i++) {
            // Rotacionar posições
            $currentPos = $positions[$posIndex % 4];
            $posIndex++;

            $name = $playerNames[$i % count($playerNames)];
            if ($i >= count($playerNames)) {
                $name .= ' ' . (int)($i / count($playerNames) + 1);
            }

            $player = Player::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . ($i + 1) . '@example.com',
                'position_id' => $currentPos->id,
            ]);

            $players[] = [
                'player' => $player,
                'category' => $this->categorizePlayer($i), // 'elite', 'regular', 'fraco'
            ];
        }

        // Atribuir estatísticas aos jogadores baseado na categoria
        foreach ($players as $playerData) {
            $player = $playerData['player'];
            $category = $playerData['category'];

            if ($category === 'elite') {
                // Jogadores elite: 25-40 jogos, muitos golos/assistências
                $gamesCount = rand(25, 40);
            } elseif ($category === 'regular') {
                // Jogadores regulares: 10-20 jogos
                $gamesCount = rand(10, 20);
            } else {
                // Jogadores fracos: 0-8 jogos
                $gamesCount = rand(0, 8);
            }

            // Atribuir jogos ao jogador
            $assignedGames = collect($this->allGames)->random(min($gamesCount, count($this->allGames)))->values();

            foreach ($assignedGames as $game) {
                $team = Team::where('game_id', $game->id)->inRandomOrder()->first();

                // Estatísticas variam por categoria
                if ($category === 'elite') {
                    // Muito boas
                    if ($player->position_code === 'FWD') {
                        $goals = rand(2, 5);
                        $assists = rand(0, 2);
                    } elseif ($player->position_code === 'MID') {
                        $goals = rand(1, 3);
                        $assists = rand(1, 3);
                    } else {
                        $goals = rand(0, 1);
                        $assists = rand(0, 1);
                    }
                } elseif ($category === 'regular') {
                    // Normais
                    if ($player->position_code === 'FWD') {
                        $goals = rand(0, 2);
                        $assists = rand(0, 1);
                    } elseif ($player->position_code === 'MID') {
                        $goals = rand(0, 1);
                        $assists = rand(0, 1);
                    } else {
                        $goals = rand(0, 0);
                        $assists = rand(0, 0);
                    }
                } else {
                    // Muito más (quase nunca marcam)
                    $goals = rand(0, 1);
                    $assists = rand(0, 0);
                }

                if (!PlayerGameStatistic::where('player_id', $player->id)->where('game_id', $game->id)->exists()) {
                    PlayerGameStatistic::create([
                        'game_id' => $game->id,
                        'player_id' => $player->id,
                        'team_id' => $team->id,
                        'position_id' => $player->position_id,
                        'goals' => $goals,
                        'assists' => $assists,
                        'is_present' => true,
                    ]);
                }
            }
        }
    }

    private function categorizePlayer($index): string
    {
        // 25% elite, 50% regular, 25% fraco
        $percentage = ($index % 100) / 100;

        if ($percentage < 0.25) {
            return 'elite';
        } elseif ($percentage < 0.75) {
            return 'regular';
        } else {
            return 'fraco';
        }
    }
}
