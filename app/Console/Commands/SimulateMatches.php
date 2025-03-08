<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Models\Team;

class SimulateMatches extends Command
{
    protected $signature = 'simulate:matches';
    protected $description = 'Simulate all group stage matches and update results';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Simulating matches...");

        $matches = FootballMatch::where('played', false)->get();

        foreach ($matches as $match) {
            $homeTeam = Team::find($match->home_team_id);
            $awayTeam = Team::find($match->away_team_id);

            if (!$homeTeam || !$awayTeam) {
                $this->error("Error: Match teams not found!");
                continue;
            }

            // Ev sahibi avantajı
            $homeAdvantage = 1.1;
            $homeStrength = $homeTeam->elo * $homeAdvantage;
            $awayStrength = $awayTeam->elo;

            $totalStrength = $homeStrength + $awayStrength;
            $homeWinProbability = $homeStrength / $totalStrength;

           
            $rand = mt_rand() / mt_getrandmax();

            // Sürpriz Maç Etkisi
            $surpriseFactor = 0.15; // %15 olasılıkla zayıf takım kazanabilir
            if ($rand < $surpriseFactor) {
                if ($homeStrength > $awayStrength) {
                    // Beklenmedik deplasman galibiyeti
                    $homeScore = rand(0, 2);
                    $awayScore = rand(1, 3);
                } else {
                    // Beklenmedik ev sahibi galibiyeti
                    $homeScore = rand(1, 3);
                    $awayScore = rand(0, 2);
                }
            } else {
                // Normal Maç Hesaplaması (Elo'ya Dayalı)
                if ($rand < $homeWinProbability) {
                    $homeScore = rand(1, 3);
                    $awayScore = rand(0, 2);
                } elseif ($rand < 0.85) { // Beraberlik ihtimali
                    $homeScore = rand(0, 2);
                    $awayScore = $homeScore;
                } else {
                    $homeScore = rand(0, 2);
                    $awayScore = rand(1, 3);
                }
            }

            
            $match->home_score = $homeScore;
            $match->away_score = $awayScore;
            $match->played = true;
            $match->save();

           
            $homeTeam->matches_played += 1;
            $awayTeam->matches_played += 1;

            if ($homeScore > $awayScore) {
                $homeTeam->wins += 1;
                $awayTeam->losses += 1;
                $homeTeam->points += 3;
            } elseif ($homeScore < $awayScore) {
                $awayTeam->wins += 1;
                $homeTeam->losses += 1;
                $awayTeam->points += 3;
            } else {
                $homeTeam->draws += 1;
                $awayTeam->draws += 1;
                $homeTeam->points += 1;
                $awayTeam->points += 1;
            }

            $homeTeam->goals_for += $homeScore;
            $homeTeam->goals_against += $awayScore;
            $awayTeam->goals_for += $awayScore;
            $awayTeam->goals_against += $homeScore;

            $homeTeam->save();
            $awayTeam->save();

            $this->info("Match: {$homeTeam->name} {$homeScore} - {$awayScore} {$awayTeam->name}");
        }

        $this->info("All matches have been simulated with surprises included!");
    }
}
