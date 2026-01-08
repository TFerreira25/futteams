<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateTeamsRequest;
use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Models\Player;
use App\Services\GameService;
use App\Services\PlayerService;
use App\Services\ResultService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function __construct(
        private GameService $gameService,
        private PlayerService $playerService,
        private ResultService $resultService
    ) {}

    public function index(): Response
    {
        $games = $this->gameService->getAll();
        $formattedGames = $games->map(fn($game) => $this->gameService->formatForFrontend($game));

        return Inertia::render('Games/Index', [
            'games' => $formattedGames,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Games/Create');
    }

    public function store(StoreGameRequest $request)
    {
        $game = $this->gameService->create($request->validated());

        return redirect()->route('games.select-players', $game->id);
    }

    public function selectPlayers(Game $game): Response
    {
        return Inertia::render('Games/SelectPlayers', [
            'game' => $this->gameService->formatForFrontend($game),
        ]);
    }

    public function generateTeams(Game $game, GenerateTeamsRequest $request)
    {
        try {
            $this->gameService->generateTeams($game, $request->validated()['player_ids']);
            return redirect()->route('games.show', $game->id);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Game $game): Response
    {
        return Inertia::render('Games/Show', [
            'game' => $this->gameService->formatForFrontend($game),
            'events' => $this->resultService->getGameEvents($game),
        ]);
    }

    public function edit(Game $game): Response
    {
        return Inertia::render('Games/Edit', [
            'game' => $this->gameService->formatForFrontend($game),
        ]);
    }

    public function update(StoreGameRequest $request, Game $game)
    {
        $updated = $this->gameService->update($game, $request->validated());

        return redirect()->route('games.index')->with('success', 'Jogo atualizado com sucesso');
    }

    public function destroy(Game $game)
    {
        $this->gameService->delete($game);

        return redirect()->route('games.index')->with('success', 'Jogo eliminado com sucesso');
    }

    public function apiGetAll(): JsonResponse
    {
        $games = $this->gameService->getAll();
        $formatted = $games->map(fn($game) => $this->gameService->formatForFrontend($game));

        return response()->json($formatted);
    }

    public function apiGetGame(Game $game): JsonResponse
    {
        return response()->json($this->gameService->formatForFrontend($game));
    }
}
