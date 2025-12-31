<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{

    protected $fillable = [
        'code',
        'started_at',
        'current_movie_id',
        'matched_movie_id',
        'continue_hunting_requested_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'continue_hunting_requested_at' => 'datetime',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(RoomParticipant::class);
    }

    public static function generateCode(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length = 6;

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (static::where('code', $code)->exists());

        return Str::upper($code);
    }
}
