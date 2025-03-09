<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KnockoutController;
use App\Http\Controllers\TournamentController;

Route::get('/team/{name}/elo', [TeamController::class, 'getTeamPower']);
Route::put('/team/{name}/update-elo', [TeamController::class, 'updateTeamPower']);
Route::post('/start-knockout-stage', [KnockoutController::class, 'startKnockoutStage']);
Route::get('/update-win-probability', [TournamentController::class, 'updateWinProbability']);