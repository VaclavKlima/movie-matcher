<?php

namespace App\Livewire;

use App\Models\Room;
use App\Actions\SuggestMovie;
use App\Models\MovieVote;
use App\Models\Movie;
use App\Models\RoomMovieMatch;
use App\Models\RoomParticipant;
use Illuminate\Support\Carbon;
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
    public ?string $lastChoiceMessage = null;
    public bool $showMatchModal = false;
    public ?int $matchedMovieId = null;
    public bool $debugSuggest = false;
    public array $debugSuggestMeta = [];

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
        $this->debugSuggest = request()->boolean('debug');
        $this->loadRandomMovie();

        if ($room->matched_movie_id) {
            $this->matchedMovieId = $room->matched_movie_id;
            $this->showMatchModal = true;
        }
    }

    public function vote(string $decision): void
    {
        if (! in_array($decision, ['up', 'down'], true)) {
            return;
        }

        if (! $this->currentMovieId) {
            return;
        }

        $movieId = $this->currentMovieId;
        if (! $movieId) {
            return;
        }

        MovieVote::updateOrCreate(
            [
                'room_id' => $this->roomId,
                'room_participant_id' => $this->participantId,
                'movie_id' => $movieId,
            ],
            ['decision' => $decision]
        );

        $this->lastChoice = $decision;
        $messageOptions = config('room.last_choice_messages.'.$decision, []);
        $this->lastChoiceMessage = $messageOptions !== []
            ? \Illuminate\Support\Arr::random($messageOptions)
            : null;

        $this->checkForMatch($movieId);
        $this->loadRandomMovie();
    }

    public function continueHunting(): void
    {
        $this->showMatchModal = false;
        $this->matchedMovieId = null;
        Room::where('id', $this->roomId)->update(['matched_movie_id' => null]);
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

        if ($room->matched_movie_id) {
            $this->matchedMovieId = $room->matched_movie_id;
            $this->showMatchModal = true;
            return;
        }

        if ($this->showMatchModal) {
            $this->showMatchModal = false;
            $this->matchedMovieId = null;
        }
    }

    public function render()
    {
        $currentMovie = $this->currentMovieId
            ? Movie::with('genres')->find($this->currentMovieId)
            : null;
        $matchedMovie = $this->matchedMovieId
            ? Movie::with('genres')->find($this->matchedMovieId)
            : null;
        $matchedMovies = RoomMovieMatch::with('movie')
            ->where('room_id', $this->roomId)
            ->orderByDesc('matched_at')
            ->get();
        $participants = RoomParticipant::where('room_id', $this->roomId)
            ->whereNull('kicked_at')
            ->orderByDesc('is_host')
            ->orderBy('created_at')
            ->get();

        return view('livewire.room-match', [
            'participants' => $participants,
            'movie' => $currentMovie,
            'matchedMovie' => $matchedMovie,
            'matchedMovies' => $matchedMovies,
        ])->layout('components.layouts.marketing', ['title' => 'Matching in '.$this->roomCode]);
    }

    protected function loadRandomMovie(): void
    {
        if ($this->debugSuggest) {
            $result = app(SuggestMovie::class)->executeWithDebug($this->roomId, $this->participantId);
            $this->currentMovieId = $result['id'];
            $this->debugSuggestMeta = $result;
            return;
        }

        $this->currentMovieId = app(SuggestMovie::class)->execute($this->roomId, $this->participantId);
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
            Room::where('id', $this->roomId)->update(['matched_movie_id' => $movieId]);
            RoomMovieMatch::updateOrCreate(
                [
                    'room_id' => $this->roomId,
                    'movie_id' => $movieId,
                ],
                [
                    'matched_at' => Carbon::now(),
                ]
            );
        }
    }
}
