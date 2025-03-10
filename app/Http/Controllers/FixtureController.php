<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FixtureController extends Controller
{
    public function generateFixtures()
    {
        try {
            Artisan::call('generate:fixtures');
            return response()->json(['message' => 'Fikstür başarıyla oluşturuldu!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getFixtures()
    {
        $fixtures = DB::table('football_matches')
            ->join('teams as home', 'football_matches.home_team_id', '=', 'home.id')
            ->join('teams as away', 'football_matches.away_team_id', '=', 'away.id')
            ->select(
                'football_matches.id',
                'football_matches.group_id',
                'football_matches.played',
                'football_matches.home_score',
                'football_matches.away_score',
                'home.name as home_team',
                'away.name as away_team'
            )
            ->orderBy('football_matches.id')
            ->get();

        return response()->json($fixtures);
    }


    public function simulateMatches()
    {
        try {
            // Artisan komutunu çalıştır
            dispatch(function () {
                Artisan::call('simulate:matches');
            });

            return response()->json(['message' => 'Maçlar oynanıyor!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getMatchResults()
    {
        $matches = DB::table('football_matches')
            ->join('teams as home', 'football_matches.home_team_id', '=', 'home.id')
            ->join('teams as away', 'football_matches.away_team_id', '=', 'away.id')
            ->select(
                'football_matches.id',
                'football_matches.group_id',
                'football_matches.played',
                'football_matches.home_score',
                'football_matches.away_score',
                'home.name as home_team',
                'away.name as away_team'
            )
            ->where('football_matches.played', 1) // Sadece oynanmış maçları getir
            ->orderBy('football_matches.id')
            ->get();

        return response()->json($matches);
    }


    


}
