<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RoomJoin extends Component
{
    public string $roomCode = '';
    public string $name = '';
    public string $avatar = 'popcorn';
    public array $avatars = [];

    public function mount(?string $code = null): void
    {
        $room = Room::where('code', $code)->firstOrFail();
        $existingParticipant = RoomParticipant::where('room_id', $room->id)
            ->where('session_id', Session::getId())
            ->first();

        if ($existingParticipant) {
            $this->redirectRoute('rooms.show', ['code' => $room->code]);
            return;
        }

        if ($room->started_at) {
            abort(403, 'This room is already matching.');
        }

        $this->roomCode = $room->code;
        $this->avatars = $this->avatarOptions();
        $this->name = $this->randomDefaultName();
        $this->avatar = $this->randomDefaultAvatar();
    }

    public function confirmJoin(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:80'],
            'avatar' => [
                'required',
                'string',
                Rule::in(collect($this->avatarOptions())->pluck('id')->all()),
            ],
        ]);

        $room = Room::where('code', $this->roomCode)->firstOrFail();
        $existingParticipant = RoomParticipant::where('room_id', $room->id)
            ->where('session_id', Session::getId())
            ->first();

        if ($existingParticipant) {
            $this->redirectRoute('rooms.show', ['code' => $room->code]);
            return;
        }

        if ($room->started_at) {
            abort(403, 'This room is already matching.');
        }

        $isHost = Session::get('host_room_code') === $room->code;

        RoomParticipant::create([
            'room_id' => $room->id,
            'session_id' => Session::getId(),
            'name' => $this->name,
            'avatar' => $this->avatar,
            'is_host' => $isHost,
            'is_ready' => $isHost,
            'last_seen_at' => Carbon::now(),
        ]);

        $this->redirectRoute('rooms.show', ['code' => $room->code]);
        return;
    }

    public function render(): View
    {
        return view('livewire.room-join')
            ->layout('components.layouts.marketing', ['title' => 'Join room '.$this->roomCode]);
    }

    protected function randomDefaultName(): string
    {
        $names = config('room.funny_names', []);
        if (! is_array($names) || $names === []) {
            return 'Guest';
        }

        return Arr::random($names);
    }

    protected function randomDefaultAvatar(): string
    {
        $avatars = $this->avatarOptions();
        if ($avatars === []) {
            return 'popcorn';
        }

        return Arr::random($avatars)['id'] ?? 'popcorn';
    }

    protected function avatarOptions(): array
    {
        $avatars = config('room.avatars', []);
        if (! is_array($avatars) || $avatars === []) {
            return [
                ['id' => 'popcorn', 'label' => 'Popcorn', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'ring' => 'ring-amber-400/40'],
            ];
        }

        return $avatars;
    }
}
