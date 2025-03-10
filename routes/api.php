<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KnockoutController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FixtureController;

Route::get('/team/{name}/elo', [TeamController::class, 'getTeamPower']);
Route::put('/team/{name}/update-elo', [TeamController::class, 'updateTeamPower']);
Route::post('/start-knockout-stage', [KnockoutController::class, 'startKnockoutStage']);
Route::get('/update-win-probability', [TournamentController::class, 'updateWinProbability']);

Route::post('/import-teams', [TeamController::class, 'importTeams']);
Route::get('/teams', [TeamController::class, 'getTeams']);

Route::post('/generate-groups', [GroupController::class, 'generateGroups']);
Route::get('/groups', [GroupController::class, 'getGroups']);

Route::post('/assign-teams', [GroupController::class, 'assignTeamsToGroups']);
Route::get('/groups-with-teams', [GroupController::class, 'getGroupsWithTeams']);

Route::post('/generate-fixtures', [FixtureController::class, 'generateFixtures']);
Route::get('/fixtures', [FixtureController::class, 'getFixtures']);

Route::post('/calculate-group-standings', [GroupController::class, 'calculateGroupStandings']);
Route::get('/group-standings', [GroupController::class, 'getGroupStandings']);
Route::get('/calculate-and-fetch-group-standings', [GroupController::class, 'calculateAndFetchGroupStandings']);



Route::post('/simulate-matches', [FixtureController::class, 'simulateMatches']);
Route::get('/match-results', [FixtureController::class, 'getMatchResults']);


