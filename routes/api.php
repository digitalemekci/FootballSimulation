<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KnockoutController;

Route::get('/team/{name}/elo', [TeamController::class, 'getTeamPower']);
Route::put('/team/{name}/update-elo', [TeamController::class, 'updateTeamPower']);
Route::post('/start-knockout-stage', [KnockoutController::class, 'startKnockoutStage']);