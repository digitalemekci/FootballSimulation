<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\Team;

class AssignTeamsToGroups extends Command
{
    protected $signature = 'assign:teams-to-groups';
    protected $description = 'Randomly assign teams to the 8 Champions League groups';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Assigning teams to groups...");

        Team::query()->update(['group_id' => null]);

        $groupNames = range('A', 'H');

        $groups = Group::whereIn('name', $groupNames)->get();

        if ($groups->count() !== 8) {
            $this->error("Error: 8 grup oluşturulmadı! Lütfen önce grupları oluşturun.");
            return;
        }

        $teams = Team::inRandomOrder()->get();

        $groupIndex = 0;

        foreach ($teams as $team) {
            $group = $groups[$groupIndex];

            $team->group_id = $group->id;
            $team->save();

            $groupIndex = ($groupIndex + 1) % 8;
        }

        $this->info("Teams assigned to groups successfully!");
    }
}
