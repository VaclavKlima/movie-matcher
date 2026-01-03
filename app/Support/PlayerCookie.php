<?php

namespace App\Support;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class PlayerCookie
{
    public static function getOrCreate(): string
    {
        $cookieName = config('room.player_cookie_name', 'moviematcher_player');
        $existing = request()->cookie($cookieName);

        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        $value = (string) Str::uuid();
        $minutes = (int) config('room.player_cookie_minutes', 60 * 24 * 365);
        Cookie::queue($cookieName, $value, $minutes);

        return $value;
    }
}
