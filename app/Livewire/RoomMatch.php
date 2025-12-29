<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\MovieVote;
use App\Models\Movie;
use App\Models\RoomParticipant;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RoomMatch extends Component
{
    public int $roomId;
    public int $participantId;
    public string $roomCode = '';
    public bool $isHost = false;
    public ?int $currentMovieId = null;
    public ?string $lastChoice = null;
    public bool $showMatchModal = false;
    public ?int $matchedMovieId = null;

    public function mount(?string $code = null): void
    {
        $room = Room::where('code', $code)->firstOrFail();
        $participant = RoomParticipant::where('room_id', $room->id)
            ->where('session_id', Session::getId())
            ->whereNull('kicked_at')
            ->first();

        if (! $room->started_at) {
            $this->redirectRoute('rooms.show', ['code' => $room->code]);
            return;
        }

        if (! $participant) {
            abort(403, 'This room is already matching.');
        }

        $this->roomId = $room->id;
        $this->participantId = $participant->id;
        $this->roomCode = $room->code;
        $this->isHost = (bool) $participant->is_host;
        $this->syncRoomState($room);
    }

    public function vote(string $decision): void
    {
        if (! in_array($decision, ['up', 'down'], true)) {
            return;
        }

        if (! $this->currentMovieId) {
            return;
        }

        MovieVote::updateOrCreate(
            [
                'room_id' => $this->roomId,
                'room_participant_id' => $this->participantId,
                'movie_id' => $this->currentMovieId,
            ],
            ['decision' => $decision]
        );

        $this->lastChoice = $decision;

        $this->checkForMatch($this->currentMovieId);
    }

    public function continueHunting(): void
    {
        $this->showMatchModal = false;
        $this->matchedMovieId = null;
        $this->advanceMovie();
    }

    public function refreshState(): void
    {
        $room = Room::find($this->roomId);
        if (! $room) {
            return;
        }

        if (! $room->started_at) {
            $this->redirectRoute('rooms.show', ['code' => $this->roomCode]);
            return;
        }

        $this->syncRoomState($room);
    }

    public function render()
    {
        $currentMovie = $this->currentMovieId
            ? Movie::with('genres')->find($this->currentMovieId)
            : null;
        $matchedMovie = $this->matchedMovieId
            ? Movie::with('genres')->find($this->matchedMovieId)
            : null;
        $participants = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->orderByDesc('is_host')
            ->orderBy('created_at')
            ->get();

        return view('livewire.room-match', [
            'participants' => $participants,
            'movie' => $currentMovie,
            'matchedMovie' => $matchedMovie,
        ])->layout('components.layouts.marketing', ['title' => 'Matching in '.$this->roomCode]);
    }

    protected function loadRandomMovieId(): ?int
    {
        return Movie::query()->inRandomOrder()->value('id');
    }

    protected function checkForMatch(int $movieId): void
    {
        $participantIds = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->pluck('id');

        if ($participantIds->isEmpty()) {
            return;
        }

        $votesCount = MovieVote::where('room_id', $this->roomId)
            ->where('movie_id', $movieId)
            ->whereIn('room_participant_id', $participantIds)
            ->distinct('room_participant_id')
            ->count('room_participant_id');

        if ($votesCount !== $participantIds->count()) {
            return;
        }

        $likesCount = MovieVote::where('room_id', $this->roomId)
            ->where('movie_id', $movieId)
            ->where('decision', 'up')
            ->whereIn('room_participant_id', $participantIds)
            ->distinct('room_participant_id')
            ->count('room_participant_id');

        if ($likesCount === $participantIds->count()) {
            Room::where('id', $this->roomId)->update([
                'matched_movie_id' => $movieId,
            ]);
            $this->matchedMovieId = $movieId;
            $this->showMatchModal = true;
            return;
        }

        $this->advanceMovie();
    }

    protected function advanceMovie(): void
    {
        $nextMovieId = $this->loadRandomMovieId();
        Room::where('id', $this->roomId)->update([
            'current_movie_id' => $nextMovieId,
            'matched_movie_id' => null,
        ]);
        $this->currentMovieId = $nextMovieId;
        $this->showMatchModal = false;
        $this->matchedMovieId = null;
    }

    protected function syncRoomState(Room $room): void
    {
        if (! $room->current_movie_id) {
            $room->update(['current_movie_id' => $this->loadRandomMovieId()]);
            $room->refresh();
        }

        $this->currentMovieId = $room->current_movie_id;
        $this->matchedMovieId = $room->matched_movie_id;
        $this->showMatchModal = $room->matched_movie_id !== null;
    }
}
