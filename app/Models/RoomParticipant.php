<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'session_id',
        'player_cookie_id',
        'name',
        'avatar',
        'is_host',
        'is_ready',
        'last_seen_at',
        'kicked_at',
    ];

    protected $casts = [
        'is_host' => 'boolean',
        'is_ready' => 'boolean',
        'last_seen_at' => 'datetime',
        'kicked_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
