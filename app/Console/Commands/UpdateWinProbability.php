<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class UpdateWinProbability extends Command
{
    protected $signature = 'update-win-probability';
    protected $description = 'Update the win probability of qualified teams';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Updating win probabilities...');

        // Tüm takımların şampiyonluk ihtimallerini sıfırla
        Team::query()->update(['win_probability' => 0]);

        // Son 16'ya kalan takımları al
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
            })->get();

        // Takımların oranlarını güncelle
        foreach ($qualifiedTeams as $team) {
            $winProbability = rand(5, 15); // Örnek değer
            DB::table('teams')
                ->where('id', $team->id)
                ->update(['win_probability' => $winProbability]);
        }

        // Elenen takımları bul (Son oynanan aşamada kaybedenler)
        $lastStage = DB::table('tournament_stages')->max('stage');
        $eliminatedTeams = DB::table('tournament_stages')
            ->where('stage', $lastStage)
            ->whereNull('winner_team_id')
            ->pluck('home_team_id')
            ->merge(
                DB::table('tournament_stages')
                ->where('stage', $lastStage)
                ->whereNull('winner_team_id')
                ->pluck('away_team_id')
            );

        // Bu takımların şampiyonluk oranını sıfırla
        DB::table('teams')->whereIn('id', $eliminatedTeams)->update(['win_probability' => 0]);

        $this->info('Win probabilities updated successfully!');
    }
}
