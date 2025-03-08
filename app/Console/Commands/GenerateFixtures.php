<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FootballMatch;
use App\Models\Group;
use App\Models\Team;

class GenerateFixtures extends Command
{
    protected $signature = 'generate:fixtures';
    protected $description = 'Generate match fixtures for the Champions League group stage';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating group stage match fixtures...");

       
        FootballMatch::truncate();

        
        $groups = Group::all();

        foreach ($groups as $group) {
            $teams = $group->teams;

            if ($teams->count() !== 4) {
                $this->error("Error: Group {$group->name} does not have exactly 4 teams!");
                continue;
            }

            
            for ($i = 0; $i < count($teams); $i++) {
                for ($j = $i + 1; $j < count($teams); $j++) {
                    
                    FootballMatch::create([
                        'group_id' => $group->id,
                        'home_team_id' => $teams[$i]->id,
                        'away_team_id' => $teams[$j]->id,
                        'played' => false
                    ]);

                    
                    FootballMatch::create([
                        'group_id' => $group->id,
                        'home_team_id' => $teams[$j]->id,
                        'away_team_id' => $teams[$i]->id,
                        'played' => false
                    ]);
                }
            }
        }

        $this->info("Group stage fixtures generated successfully!");
    }
}
