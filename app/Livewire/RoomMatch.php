<?php

namespace App\Livewire;

use App\Actions\SuggestMovie;
use App\Models\Movie;
use App\Models\MovieVote;
use App\Models\Room;
use App\Models\RoomMovieMatch;
use App\Models\RoomParticipant;
use App\Support\PlayerCookie;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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

    public ?string $lastContinueRequestedAt = null;

    public function mount(?string $code = null): void
    {
        $room = Room::where('code', $code)->firstOrFail();
        $playerCookieId = PlayerCookie::getOrCreate();
        if ($room->ended_at) {
            $this->redirectRoute('rooms.stats', ['code' => $room->code]);

            return;
        }
        $participant = RoomParticipant::where('room_id', $room->id)
            ->where('player_cookie_id', $playerCookieId)
            ->whereNull('kicked_at')
            ->first();

        if (! $participant) {
            $participant = RoomParticipant::where('room_id', $room->id)
                ->where('session_id', Session::getId())
                ->whereNull('kicked_at')
                ->first();

            if ($participant && ! $participant->player_cookie_id) {
                $participant->update(['player_cookie_id' => $playerCookieId]);
            }
        }

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
        $this->lastContinueRequestedAt = $room->continue_hunting_requested_at?->toISOString();
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
        Cache::tags(["movie_matcher_room_{$this->roomId}"])->flush();
    }

    public function continueHunting(): void
    {
        $this->showMatchModal = false;
        $this->matchedMovieId = null;

        $now = Carbon::now();
        Room::where('id', $this->roomId)->update([
            'matched_movie_id' => null,
            'continue_hunting_requested_at' => $now,
        ]);
    }

    public function endRoomWithMatch(?int $movieId = null): void
    {
        if (! $this->isHost) {
            return;
        }

        $selectedMovieId = $movieId ?? $this->matchedMovieId;
        if (! $selectedMovieId) {
            return;
        }

        $isMatchedMovie = RoomMovieMatch::where('room_id', $this->roomId)
            ->where('movie_id', $selectedMovieId)
            ->exists();

        if (! $isMatchedMovie && $selectedMovieId !== $this->matchedMovieId) {
            return;
        }

        RoomMovieMatch::updateOrCreate(
            [
                'room_id' => $this->roomId,
                'movie_id' => $selectedMovieId,
            ],
            [
                'matched_at' => Carbon::now(),
            ]
        );

        Room::where('id', $this->roomId)->update([
            'matched_movie_id' => $selectedMovieId,
            'ended_at' => Carbon::now(),
        ]);

        $this->redirectRoute('rooms.stats', ['code' => $this->roomCode]);

    }

    public function refreshState(): void
    {
        // Optimize: Only fetch needed columns
        $room = Room::select('id', 'started_at', 'matched_movie_id', 'continue_hunting_requested_at', 'ended_at')
            ->find($this->roomId);

        if (! $room) {
            $this->redirectRoute('home');

            return;
        }
        if ($room->ended_at) {
            $this->redirectRoute('rooms.stats', ['code' => $this->roomCode]);

            return;
        }

        // Optimize: Only fetch the columns we need
        $participant = RoomParticipant::select('id', 'kicked_at')
            ->where('id', $this->participantId)
            ->first();

        if (! $participant || $participant->kicked_at !== null) {
            $this->redirectRoute('home');

            return;
        }

        if (! $room->started_at) {
            $this->redirectRoute('rooms.show', ['code' => $this->roomCode]);

            return;
        }

        // Check for continue hunting request
        $continueRequestedAt = $room->continue_hunting_requested_at?->toISOString();
        if ($continueRequestedAt && $continueRequestedAt !== $this->lastContinueRequestedAt) {
            $this->lastContinueRequestedAt = $continueRequestedAt;

            $this->dispatch('toast',
                message: '🎬 Continuing the hunt! Time to find another gem...',
                type: 'info'
            );
        }

        // Check for match
        if ($room->matched_movie_id && $room->matched_movie_id !== $this->matchedMovieId) {
            $this->matchedMovieId = $room->matched_movie_id;
            $this->showMatchModal = true;

            return;
        }

        // Close modal if match was cleared
        if ($this->showMatchModal && ! $room->matched_movie_id) {
            $this->showMatchModal = false;
            $this->matchedMovieId = null;
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
        $voteStats = MovieVote::where('room_id', $this->roomId)
            ->get(['room_participant_id', 'decision']);
        $voteStatsByParticipant = $voteStats
            ->groupBy('room_participant_id')
            ->map(function ($votes) {
                return [
                    'up' => $votes->where('decision', 'up')->count(),
                    'down' => $votes->where('decision', 'down')->count(),
                ];
            });

        return view('livewire.room-match', [
            'participants' => $participants,
            'movie' => $currentMovie,
            'matchedMovie' => $matchedMovie,
            'matchedMovies' => $matchedMovies,
            'voteStatsByParticipant' => $voteStatsByParticipant,
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
