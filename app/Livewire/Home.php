<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $cookieName = config('room.player_cookie_name', 'moviematcher_player');
        $playerCookieId = request()->cookie($cookieName);
        $finishedRooms = collect();

        if (is_string($playerCookieId) && $playerCookieId !== '') {
            $finishedRooms = Room::query()
                ->select('rooms.id', 'rooms.code', 'rooms.ended_at', 'rooms.matched_movie_id')
                ->join('room_participants', 'room_participants.room_id', '=', 'rooms.id')
                ->where('room_participants.player_cookie_id', $playerCookieId)
                ->whereNotNull('rooms.ended_at')
                ->orderByDesc('rooms.ended_at')
                ->limit(2)
                ->get();
        }

        return view('livewire.home', [
            'finishedRooms' => $finishedRooms,
            'hasPlayerCookie' => is_string($playerCookieId) && $playerCookieId !== '',
        ])
            ->layout('components.layouts.marketing', ['title' => 'Movie Matcher']);
    }
}
