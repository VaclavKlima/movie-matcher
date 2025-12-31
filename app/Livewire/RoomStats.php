<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class RoomStats extends Component
{
    public string $roomCode = '';

    public function mount(?string $code = null): void
    {
        $room = Room::where('code', $code)->firstOrFail();

        $this->roomCode = $room->code;
    }

    public function render()
    {
        return view('livewire.room-stats')
            ->layout('components.layouts.marketing', ['title' => 'Stats for '.$this->roomCode]);
    }
}
