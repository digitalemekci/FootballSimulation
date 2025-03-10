<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function generateGroups()
    {
        try {
            // Artisan komutunu arka planda çalıştır
            dispatch(function () {
                Artisan::call('generate:groups');
            });

            return response()->json(['message' => 'Gruplar oluşturuluyor!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getGroups()
    {
        return response()->json(DB::table('groups')->get());
    }

    public function assignTeamsToGroups()
    {
        try {
            // Artisan komutunu arka planda çalıştır
            dispatch(function () {
                Artisan::call('assign:teams-to-groups');
            });

            return response()->json(['message' => 'Takımlar gruplara atanıyor!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getGroupsWithTeams()
    {
        $groups = \DB::table('groups')
            ->leftJoin('teams', 'teams.group_id', '=', 'groups.id')
            ->select('groups.id as group_id', 'groups.name as group_name', 'teams.name as team_name')
            ->get();

        $formattedGroups = [];

        foreach ($groups as $group) {
            if (!isset($formattedGroups[$group->group_id])) {
                $formattedGroups[$group->group_id] = [
                    'name' => $group->group_name,
                    'teams' => []
                ];
            }
            if ($group->team_name) {
                $formattedGroups[$group->group_id]['teams'][] = $group->team_name;
            }
        }

        return response()->json(array_values($formattedGroups));
    }

    public function getGroupStandings()
    {
        $standings = DB::table('teams')
            ->join('group_teams', 'teams.id', '=', 'group_teams.team_id')
            ->join('groups', 'group_teams.group_id', '=', 'groups.id')
            ->leftJoin('football_matches', function ($join) {
                $join->on('teams.id', '=', 'football_matches.home_team_id')
                    ->orOn('teams.id', '=', 'football_matches.away_team_id');
            })
            ->select(
                'groups.name as group_name',
                'teams.name as team_name',
                DB::raw('SUM(CASE WHEN football_matches.home_team_id = teams.id AND football_matches.home_score > football_matches.away_score THEN 3 ELSE 0 END +
                         CASE WHEN football_matches.away_team_id = teams.id AND football_matches.away_score > football_matches.home_score THEN 3 ELSE 0 END +
                         CASE WHEN football_matches.home_team_id = teams.id AND football_matches.home_score = football_matches.away_score THEN 1 ELSE 0 END +
                         CASE WHEN football_matches.away_team_id = teams.id AND football_matches.away_score = football_matches.home_score THEN 1 ELSE 0 END) AS points')
            )
            ->groupBy('teams.id', 'groups.name')
            ->orderBy('groups.name')
            ->orderByDesc('points')
            ->get();

        return response()->json($standings);
    }

    public function calculateAndFetchGroupStandings()
    {
        try {
            Artisan::call('calculate:group-standings');

            return $this->getGroupStandings();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }



}
