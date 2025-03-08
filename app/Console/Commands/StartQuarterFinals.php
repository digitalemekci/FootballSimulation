<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentStage;

class StartQuarterFinals extends Command
{
    protected $signature = 'start:quarter-finals';
    protected $description = 'Generate quarter-final matchups for the tournament';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating quarter-final matchups...");

        $quarterFinalTeams = TournamentStage::where('stage', 'Son 16')
            ->where('played', true)
            ->pluck('winner_team_id')
            ->toArray();

        if (count($quarterFinalTeams) === 8) {
            $quarterFinalMatches = [
                [$quarterFinalTeams[0], $quarterFinalTeams[7]],
                [$quarterFinalTeams[1], $quarterFinalTeams[6]],
                [$quarterFinalTeams[2], $quarterFinalTeams[5]],
                [$quarterFinalTeams[3], $quarterFinalTeams[4]],
            ];

            foreach ($quarterFinalMatches as $match) {
                TournamentStage::create([
                    'stage' => 'Çeyrek Final',
                    'home_team_id' => $match[0],
                    'away_team_id' => $match[1],
                    'played' => false,
                ]);
            }

            $this->info("✅ Çeyrek Final aşaması başarıyla oluşturuldu!");
        } else {
            $this->error("⚠️ Hata: Son 16 kazananları eksik!");
        }
    }
}
