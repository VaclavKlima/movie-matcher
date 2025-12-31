<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RoomLobby extends Component
{
    public int $roomId;

    public int $participantId;

    public string $roomCode = '';

    public string $shareUrl = '';

    public string $name = '';

    public string $avatar = 'popcorn';

    public bool $isHost = false;

    public bool $isKicked = false;

    public bool $isReady = false;

    public array $avatars = [];

    public array $knownParticipantIds = [];

    public function mount(?string $code = null): void
    {
        $this->avatars = config('room.avatars', []);
        if (! is_array($this->avatars) || $this->avatars === []) {
            $this->avatars = [
                ['id' => 'popcorn', 'label' => 'Popcorn', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'ring' => 'ring-amber-400/40'],
            ];
        }

        if (Route::is('rooms.create')) {
            $room = Room::create([
                'code' => Room::generateCode(),
            ]);

            Session::put('host_room_code', $room->code);

            $this->redirectRoute('rooms.join', ['code' => $room->code]);

            return;
        }

        $room = Room::where('code', $code)->firstOrFail();
        $isHost = Session::get('host_room_code') === $room->code;
        $existingParticipant = RoomParticipant::where('room_id', $room->id)
            ->where('session_id', Session::getId())
            ->first();

        if (! $existingParticipant) {
            if ($room->started_at) {
                abort(403, 'This room is already matching.');
            }

            $this->redirectRoute('rooms.join', ['code' => $room->code]);

            return;
        }

        $participant = $existingParticipant;
        if ($isHost && ! $participant->is_host) {
            $participant->update(['is_host' => true]);
        }
        if ($isHost && ! $participant->is_ready) {
            $participant->update(['is_ready' => true]);
            $participant->is_ready = true;
        }

        $this->roomId = $room->id;
        $this->participantId = $participant->id;
        $this->roomCode = $room->code;
        $this->shareUrl = route('rooms.join', ['code' => $room->code]);
        $this->name = $participant->name ?? '';
        $this->avatar = $participant->avatar ?? $this->avatar;
        $this->isHost = $isHost;
        $this->isKicked = $participant->kicked_at !== null;
        $this->isReady = (bool) $participant->is_ready;

        $this->knownParticipantIds = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->pluck('id')
            ->all();
        $this->touchParticipant();

        if ($room->started_at && ! $this->isKicked) {
            $this->redirectRoute('rooms.match', ['code' => $room->code]);

            return;
        }
    }

    public function updatedName(): void
    {
        if ($this->isKicked) {
            return;
        }

        $this->participant()->update(['name' => $this->name]);
    }

    public function updatedAvatar(): void
    {
        if ($this->isKicked) {
            return;
        }

        $this->participant()->update(['avatar' => $this->avatar]);
    }

    public function refreshParticipants(): void
    {
        $currentParticipant = RoomParticipant::where('id', $this->participantId)->first();
        if (! $currentParticipant || $currentParticipant->kicked_at !== null) {
            $this->isKicked = true;

            return;
        }

        $roomStarted = Room::where('id', $this->roomId)->value('started_at');
        if ($roomStarted) {
            $this->redirectRoute('rooms.match', ['code' => $this->roomCode]);

            return;
        }

        $participantIds = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->pluck('id')
            ->all();

        $newIds = array_values(array_diff($participantIds, $this->knownParticipantIds));
        if ($newIds) {
            $newNames = RoomParticipant::whereIn('id', $newIds)
                ->where('id', '!=', $this->participantId)
                ->pluck('name')
                ->filter()
                ->values()
                ->all();

            if ($newNames) {
                $nameList = implode(', ', $newNames);
                $this->dispatch('toast', message: "Joined: {$nameList}", type: 'success');
            } elseif (count($newIds) > 0) {
                $this->dispatch('toast', message: 'A guest joined the room', type: 'success');
            }
        }

        $this->knownParticipantIds = $participantIds;
        $this->touchParticipant();
    }

    public function kickParticipant(int $participantId): void
    {
        if (! $this->isHost || $participantId === $this->participantId) {
            return;
        }

        $updated = RoomParticipant::where('id', $participantId)
            ->where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->update(['kicked_at' => Carbon::now()]);

        if ($updated) {
            $this->dispatch('toast', message: 'Guest was yeeted from the room', type: 'success');
        }
    }

    public function disbandRoom(): void
    {
        if (! $this->isHost) {
            return;
        }

        $room = Room::find($this->roomId);
        if (! $room) {
            $this->redirectRoute('home');

            return;
        }

        RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->update(['kicked_at' => Carbon::now()]);

        $room->delete();

        $this->redirectRoute('home');

    }

    public function startMatching(): void
    {
        if (! $this->isHost || $this->isKicked) {
            return;
        }

        $roomStarted = Room::where('id', $this->roomId)->value('started_at');
        if ($roomStarted) {
            $this->redirectRoute('rooms.match', ['code' => $this->roomCode]);

            return;
        }

        $participants = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->get();

        $hasEnoughPlayers = $participants->count() >= 2;
        $everyoneReady = $participants->every(fn ($participant) => (bool) $participant->is_ready);

        if (! $hasEnoughPlayers || ! $everyoneReady) {
            return;
        }

        Room::where('id', $this->roomId)->update(['started_at' => Carbon::now()]);
        $this->redirectRoute('rooms.match', ['code' => $this->roomCode]);
    }

    public function toggleReady(): void
    {
        if ($this->isKicked) {
            return;
        }

        $this->isReady = ! $this->isReady;
        $this->participant()->update(['is_ready' => $this->isReady]);
    }

    public function render(): View
    {
        $participants = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->orderByDesc('is_host')
            ->orderBy('created_at')
            ->get();

        return view('livewire.room-lobby', [
            'participants' => $participants,
        ])->layout('components.layouts.marketing', ['title' => 'Room '.$this->roomCode]);
    }

    protected function participant(): RoomParticipant
    {
        return RoomParticipant::where('id', $this->participantId)->firstOrFail();
    }

    protected function touchParticipant(): void
    {
        if ($this->isKicked) {
            return;
        }

        RoomParticipant::where('id', $this->participantId)
            ->update(['last_seen_at' => Carbon::now()]);
    }
}
