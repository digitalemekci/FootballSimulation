<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\Team;

class GenerateGroups extends Command
{
    protected $signature = 'generate:groups';
    protected $description = 'Randomly assign teams to 8 groups';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Generating Champions League groups...");

        $groupNames = range('A', 'H'); // A'dan H'ye kadar grup isimleri
        Team::query()->update(['group_id' => null]); // Önce tüm takımları sıfırla

        foreach ($groupNames as $name) {
            Group::firstOrCreate(['name' => $name]);
        }

        $teams = Team::inRandomOrder()->get();
        $groupIndex = 0;

        foreach ($teams as $team) {
            $group = Group::where('name', $groupNames[$groupIndex])->first();
            $team->group_id = $group->id;
            $team->save();

            $groupIndex = ($groupIndex + 1) % 8; // 8 gruba eşit dağıt
        }

        $this->info("Groups generated successfully!");
    }
}
