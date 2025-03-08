<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Team;

class ImportChampionsLeagueTeams extends Command
{
    protected $signature = 'import:champions-league-teams';
    protected $description = 'Fetch UEFA Champions League teams from Laravel API and save them to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Fetching UEFA Champions League teams from local Laravel API...");

        $client = new Client();

        // 🔥 UEFA Champions League'deki takımları belirle
        $championsLeagueTeams = [
            "ManCity", "RealMadrid", "Bayern", "Barcelona", "ParisSG", 
            "Inter", "Arsenal", "Atletico", "Napoli", "Dortmund",
            "RBLeipzig", "Newcastle", "Milan", "Benfica", "Porto",
            "Feyenoord", "Galatasaray", "Lazio", "Shakhtar", "Celtic",
            "CrvenaZvezda", "YoungBoys", "UnionBerlin", "Antwerp",
            "Liverpool", "Leverkusen", "Chelsea", "AstonVilla", "Lille", 
            "Tottenham", "Brighton", "Fenerbahce",
        ];

        $inserted = 0;
        foreach ($championsLeagueTeams as $teamName) {
            try {
                // Laravel API'den takımı çek
                $response = $client->get("http://127.0.0.1:8000/api/team/" . urlencode($teamName) . "/elo");
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['elo'])) {
                    $existingTeam = Team::where('name', $teamName)->first();
                    
                    if (!$existingTeam) {
                        Team::create([
                            'name' => $teamName,
                            'elo' => (int) $data['elo'],
                            'matches_played' => 0,
                            'wins' => 0,
                            'draws' => 0,
                            'losses' => 0,
                            'goals_for' => 0,
                            'goals_against' => 0,
                            'points' => 0
                        ]);
                        $inserted++;
                    }
                }

            } catch (\Exception $e) {
                $this->error("Error fetching data for $teamName: " . $e->getMessage());
            }
        }

        $this->info("$inserted UEFA Champions League teams inserted into the database.");
    }
}
