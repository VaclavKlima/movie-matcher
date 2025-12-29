<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieVote extends Model
{
    protected $fillable = [
        'room_id',
        'room_participant_id',
        'movie_id',
        'decision',
    ];
}
