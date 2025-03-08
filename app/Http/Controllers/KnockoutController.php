<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentStage;

class KnockoutController extends Controller
{
    public function startKnockoutStage()
    {
        $qualifiedTeams = TournamentStage::where('stage', 'Son 16')
            ->where('played', true)
            ->pluck('winner_team_id')
            ->toArray();

        if (count($qualifiedTeams) === 8) {
            $quarterFinalMatches = [
                [$qualifiedTeams[0], $qualifiedTeams[7]],
                [$qualifiedTeams[1], $qualifiedTeams[6]],
                [$qualifiedTeams[2], $qualifiedTeams[5]],
                [$qualifiedTeams[3], $qualifiedTeams[4]],
            ];

            foreach ($quarterFinalMatches as $match) {
                TournamentStage::create([
                    'stage' => 'Çeyrek Final',
                    'home_team_id' => $match[0],
                    'away_team_id' => $match[1],
                    'played' => false,
                ]);
            }

            return response()->json(['message' => '✅ Çeyrek Final aşaması oluşturuldu!'], 200);
        }

        return response()->json(['message' => '⚠️ Hata: Son 16 kazananları eksik!'], 400);
    }
}
