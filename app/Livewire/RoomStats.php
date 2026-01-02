<?php

namespace App\Livewire;

use App\Actions\GetRoomStats;
use App\Models\Room;
use App\Support\PlayerCookie;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RoomStats extends Component
{
    public int $roomId;

    public string $roomCode = '';

    public function mount(?string $code = null): void
    {
        $room = Room::where('code', $code)->firstOrFail();

        $this->roomId = $room->id;
        $this->roomCode = $room->code;
    }

    public function render(GetRoomStats $getRoomStats)
    {
        $playerCookieId = PlayerCookie::getOrCreate();
        $roomId = $this->roomId;
        $cacheKey = "room-stats:{$roomId}:{$playerCookieId}";

        $stats = Cache::rememberForever($cacheKey, function () use ($getRoomStats, $roomId, $playerCookieId) {
            return $getRoomStats->handle($roomId, $playerCookieId);
        });

        return view('livewire.room-stats', [
            'stats' => $stats,
        ])->layout('components.layouts.marketing', ['title' => 'Stats for '.$this->roomCode]);
    }
}
