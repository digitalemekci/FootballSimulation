<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentStage;
use App\Models\Team;
use App\Http\Controllers\TournamentController;

class SimulateKnockoutMatches extends Command
{
    protected $signature = 'simulate:knockout-matches';
    protected $description = 'Simulate all knockout stage matches and update results';

    public function __construct(TournamentController $tournamentController)
    {
        parent::__construct();
        $this->tournamentController = $tournamentController;
    }

    public function handle()
    {
        $this->info("Simulating knockout stage matches...");

        $matches = TournamentStage::where('played', false)->get();

        foreach ($matches as $match) {
            $homeTeam = Team::find($match->home_team_id);
            $awayTeam = Team::find($match->away_team_id);

            if (!$homeTeam || !$awayTeam) {
                $this->error("Error: Match teams not found!");
                continue;
            }

            $homeAdvantage = 1.1;
            $homeStrength = $homeTeam->elo * $homeAdvantage;
            $awayStrength = $awayTeam->elo;

            $totalStrength = $homeStrength + $awayStrength;
            $homeWinProbability = $homeStrength / $totalStrength;

            $rand = mt_rand() / mt_getrandmax();

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

            if ($homeScore == $awayScore) {
                $this->info("Match {$homeTeam->name} vs {$awayTeam->name} ended in a draw! Going to extra time...");

                // Uzatma süresi
                $extraHome = rand(0, 1);
                $extraAway = rand(0, 1);
                $homeScore += $extraHome;
                $awayScore += $extraAway;

                if ($homeScore == $awayScore) {
                    $this->info("Match still tied after extra time! Going to penalties...");

                    // Penaltılar
                    $penaltyWinner = rand(0, 1);
                    if ($penaltyWinner == 0) {
                        $homeScore += 1;
                    } else {
                        $awayScore += 1;
                    }
                }
            }

            
            if ($homeScore > $awayScore) {
                $winner = $homeTeam;
            } else {
                $winner = $awayTeam;
            }

            $match->home_score = $homeScore;
            $match->away_score = $awayScore;
            $match->played = true;
            $match->winner_team_id = $winner->id;
            $match->save();

            $this->info("Match: {$homeTeam->name} {$homeScore} - {$awayScore} {$awayTeam->name}");
            $this->info("Winner: {$winner->name} advances to the next round! 🎉");
        }

        $this->info("All knockout matches have been simulated!");
        // Şampiyonluk ihtimallerini güncelle
        $this->tournamentController->updateWinProbability();

        $this->info("✅ Championship odds updated!");
    }
}
