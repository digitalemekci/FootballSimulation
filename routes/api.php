<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

Route::get('/team/{name}/elo', [TeamController::class, 'getTeamPower']);
Route::put('/team/{name}/update-elo', [TeamController::class, 'updateTeamPower']);
