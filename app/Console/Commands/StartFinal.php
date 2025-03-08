<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentStage;

class StartFinal extends Command
{
    protected $signature = 'start:final';
    protected $description = 'Generate the final match for the tournament';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating the final match...");

        $finalTeams = TournamentStage::where('stage', 'Yarı Final')
            ->where('played', true)
            ->pluck('winner_team_id')
            ->toArray();

        if (count($finalTeams) === 2) {
            TournamentStage::create([
                'stage' => 'Final',
                'home_team_id' => $finalTeams[0],
                'away_team_id' => $finalTeams[1],
                'played' => false,
            ]);

            $this->info("✅ Final maçı başarıyla oluşturuldu!");
        } else {
            $this->error("⚠️ Hata: Yarı Final kazananları eksik!");
        }
    }
}
