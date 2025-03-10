<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Client;
use App\Models\Team;

class TeamController extends Controller
{
    /**
     * ClubElo API üzerinden takımın Elo puanını alır.
     */
    public function getTeamPower($teamName)
    {
        $client = new Client();
        $response = $client->get("http://api.clubelo.com/{$teamName}");
        $data = $response->getBody()->getContents();
        $rows = array_map('str_getcsv', explode("\n", $data));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) == count($header)) {
                $teamData = array_combine($header, $row);
                return response()->json([
                    'team' => $teamName,
                    'elo' => (int) $teamData['Elo']
                ]);
            }
        }

        return response()->json([
            'error' => 'Takım bulunamadı'
        ], 404);
    }

    /**
     * API'den çekilen Elo puanı ile veritabanındaki takımı günceller.
     */
    public function updateTeamPower($teamName)
    {
        $team = Team::where('name', $teamName)->first();

        if (!$team) {
            return response()->json(['error' => 'Takım bulunamadı'], 404);
        }

        $client = new Client();
        $response = $client->get("http://api.clubelo.com/{$teamName}");
        $data = $response->getBody()->getContents();
        $rows = array_map('str_getcsv', explode("\n", $data));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) == count($header)) {
                $teamData = array_combine($header, $row);
                $team->elo = (int) $teamData['Elo'];
                $team->save();

                return response()->json([
                    'message' => 'Takım güncellendi',
                    'team' => $team->name,
                    'elo' => $team->elo
                ]);
            }
        }

        return response()->json([
            'error' => 'Elo verisi alınamadı'
        ], 500);
    }


    public function importTeams()
    {
        try {
            // Artisan komutunu kuyrukta çalıştır, böylece API hızlı yanıt döner
            dispatch(function () {
                Artisan::call('import:champions-league-teams');
                Log::info('Takımlar başarıyla içe aktarıldı.');
            });

            return response()->json(['message' => 'Takımlar yükleme işlemi başlatıldı!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function getTeams()
    {
        return response()->json(\DB::table('teams')->get());
    }

}
