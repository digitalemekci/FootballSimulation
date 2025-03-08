<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 
        'home_team_id', 
        'away_team_id', 
        'home_score', 
        'away_score', 
        'played'
    ];

    // Grup ile ilişki
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Ev sahibi takım ile ilişki
    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    // Deplasman takımı ile ilişki
    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
