<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'elo', 'matches_played', 'wins', 'draws', 'losses', 'goals_for', 'goals_against', 'points'
    ];
}
