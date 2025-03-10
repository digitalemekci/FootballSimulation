<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class CalculateGroupStandings extends Command
{
    protected $signature = 'calculate:group-standings';
    protected $description = 'Calculate and display group standings based on match results';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Calculating group standings...");

        $groups = Group::all();

        foreach ($groups as $group) {
            $teams = $group->teams()
                ->orderByDesc('points')
                ->orderByDesc(DB::raw('goals_for - goals_against'))
                ->orderByDesc('goals_for')
                ->get();

            $this->info("Group {$group->name} Standings:");

            foreach ($teams as $index => $team) {
                $goalDifference = $team->goals_for - $team->goals_against;
                $this->info(($index + 1) . ". {$team->name} - {$team->points} pts | {$team->wins}W - {$team->draws}D - {$team->losses}L | GD: {$goalDifference}");
            }

            $this->info("------------------------------");
        }

        $this->info("Group standings calculated successfully!");
    }
}
