<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMovieMatch extends Model
{
    protected $fillable = [
        'room_id',
        'movie_id',
        'matched_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
