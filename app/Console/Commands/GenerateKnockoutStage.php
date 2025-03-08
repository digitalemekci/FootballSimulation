<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentStage;
use Illuminate\Support\Facades\DB;

class GenerateKnockoutStage extends Command
{
    protected $signature = 'generate:knockout-stage';
    protected $description = 'Generate knockout stage matchups for the tournament';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating knockout stage matchups...");

        TournamentStage::whereIn('stage', ['Son 16', 'Çeyrek Final', 'Yarı Final', 'Final'])->delete();

        // Gruplardan çıkan takımlar
        $qualifiedTeams = DB::table('teams')
            ->whereNotNull('group_id')
            ->orderBy('group_id')
            ->orderBy('points', 'desc')
            ->limit(16)
            ->pluck('id')
            ->toArray();
    

        // Son 16 maçları
        if (count($qualifiedTeams) !== 16) {
            $this->error("Hata: 16 takım bulunamadı!");
            return;
        }

        $matches = [
            [$qualifiedTeams[0], $qualifiedTeams[15]],
            [$qualifiedTeams[1], $qualifiedTeams[14]],
            [$qualifiedTeams[2], $qualifiedTeams[13]],
            [$qualifiedTeams[3], $qualifiedTeams[12]],
            [$qualifiedTeams[4], $qualifiedTeams[11]],
            [$qualifiedTeams[5], $qualifiedTeams[10]],
            [$qualifiedTeams[6], $qualifiedTeams[9]],
            [$qualifiedTeams[7], $qualifiedTeams[8]],
        ];

        foreach ($matches as $match) {
            TournamentStage::create([
                'stage' => 'Son 16',
                'home_team_id' => $match[0],
                'away_team_id' => $match[1],
                'played' => false,
            ]);
        }

        $this->info("✅ Son 16 aşaması başarıyla oluşturuldu!");
    }
}
