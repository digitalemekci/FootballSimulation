<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentStage;

class StartSemiFinals extends Command
{
    protected $signature = 'start:semi-finals';
    protected $description = 'Generate semi-final matchups for the tournament';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating semi-final matchups...");

        $semiFinalTeams = TournamentStage::where('stage', 'Çeyrek Final')
            ->where('played', true)
            ->pluck('winner_team_id')
            ->toArray();

        if (count($semiFinalTeams) === 4) {
            $semiFinalMatches = [
                [$semiFinalTeams[0], $semiFinalTeams[3]],
                [$semiFinalTeams[1], $semiFinalTeams[2]],
            ];

            foreach ($semiFinalMatches as $match) {
                TournamentStage::create([
                    'stage' => 'Yarı Final',
                    'home_team_id' => $match[0],
                    'away_team_id' => $match[1],
                    'played' => false,
                ]);
            }

            $this->info("✅ Yarı Final aşaması başarıyla oluşturuldu!");
        } else {
            $this->error("⚠️ Hata: Çeyrek Final kazananları eksik!");
        }
    }
}
