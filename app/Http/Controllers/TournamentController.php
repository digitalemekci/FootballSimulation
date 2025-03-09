<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TournamentController extends Controller
{
    public function updateWinProbability()
    {
        // Önce tüm takımların win_probability değerini sıfırla
        Team::query()->update(['win_probability' => 0]);

        // Gruptan çıkan 16 takımı al
        $qualifiedTeams = DB::table('teams')
            ->whereIn('id', function ($query) {
                $query->select('home_team_id')
                    ->from('tournament_stages')
                    ->where('stage', 'Son 16')
                    ->union(
                        DB::table('tournament_stages')
                            ->select('away_team_id')
                            ->where('stage', 'Son 16')
                    );
            })
            ->get();

        // Her takımın puanına, attığı gole ve averajına göre hesaplama yap
        foreach ($qualifiedTeams as $team) {
            $baseProbability = 2.0; // Başlangıç oranı
            $bonusPoints = $team->points * 0.2; // Her puan için 0.2 ekleyelim
            $goalDifferenceBonus = ($team->goals_for - $team->goals_against) * 0.1; // Averaj bonusu

            $finalProbability = $baseProbability + $bonusPoints + $goalDifferenceBonus;

            // Güncelle
            Team::where('id', $team->id)->update(['win_probability' => $finalProbability]);
        }

        return response()->json(['message' => 'Win probabilities updated successfully!']);
    }

}
