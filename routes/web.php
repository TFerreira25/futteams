<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RankingController::class, 'dashboard'])->name('home');
Route::get('/dashboard', [RankingController::class, 'dashboard'])->name('dashboard');
Route::resource('positions', PositionController::class);
Route::resource('players', PlayerController::class);
Route::resource('games', GameController::class);
Route::get('/games/{game}/select-players', [GameController::class, 'selectPlayers'])->name('games.select-players');
Route::post('/games/{game}/generate-teams', [GameController::class, 'generateTeams'])->name('games.generate-teams');
Route::post('/games/{game}/record-event', [ResultController::class, 'recordEvent'])->name('games.record-event');
Route::post('/games/{game}/undo-event', [ResultController::class, 'undoEvent'])->name('games.undo-event');
Route::post('/games/{game}/complete', [ResultController::class, 'completeGame'])->name('games.complete');
Route::get('/games/{game}/summary', [ResultController::class, 'getSummary'])->name('games.summary');
Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
Route::get('/rankings/goals-per-game', [RankingController::class, 'goalsPerGame'])->name('rankings.goals-per-game');
Route::get('/rankings/assists-per-game', [RankingController::class, 'assistsPerGame'])->name('rankings.assists-per-game');
Route::get('/rankings/total-goals', [RankingController::class, 'totalGoals'])->name('rankings.total-goals');
Route::get('/rankings/presence', [RankingController::class, 'presence'])->name('rankings.presence');
Route::get('/rankings/position/{position}', [RankingController::class, 'byPosition'])->name('rankings.by-position');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/players', [PlayerController::class, 'apiGetAll']);
    Route::get('/players/active', [PlayerController::class, 'apiGetActive']);
    Route::get('/players/{player}/stats', [PlayerController::class, 'apiGetStats']);
    Route::get('/games', [GameController::class, 'apiGetAll']);
    Route::get('/games/{game}', [GameController::class, 'apiGetGame']);
    Route::post('/games/{game}/record-event', [ResultController::class, 'recordEvent']);
    Route::post('/games/{game}/undo-event', [ResultController::class, 'undoEvent']);
    Route::post('/games/{game}/complete', [ResultController::class, 'completeGame']);
    Route::get('/games/{game}/statistics', [ResultController::class, 'apiGetGameStatistics']);
    Route::get('/games/{game}/events', [ResultController::class, 'apiGetGameEvents']);
    Route::get('/rankings/dashboard', [RankingController::class, 'apiDashboard']);
    Route::get('/rankings/goals-per-game', [RankingController::class, 'apiGoalsPerGame']);
    Route::get('/rankings/assists-per-game', [RankingController::class, 'apiAssistsPerGame']);
    Route::get('/rankings/total-goals', [RankingController::class, 'apiTotalGoals']);
    Route::get('/rankings/presence', [RankingController::class, 'apiPresence']);
    Route::get('/rankings/position/{position}', [RankingController::class, 'apiByPosition']);
    Route::get('/rankings/compare/{player1Id}/{player2Id}', [RankingController::class, 'apiCompare']);
});
