<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\MovieVote;
use App\Models\Room;
use App\Models\RoomMovieMatch;
use App\Models\RoomParticipant;
use App\Support\PlayerCookie;
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

    public function render()
    {
        $room = Room::findOrFail($this->roomId);
        $participants = RoomParticipant::where('room_id', $this->roomId)
            ->orderByDesc('is_host')
            ->orderBy('created_at')
            ->get();

        $playerCookieId = PlayerCookie::getOrCreate();
        $currentParticipant = $participants->firstWhere('player_cookie_id', $playerCookieId);

        $selectedMovie = $room->matched_movie_id
            ? Movie::with(['genres', 'actors'])->find($room->matched_movie_id)
            : null;

        $matchedMovies = RoomMovieMatch::with(['movie.genres', 'movie.actors'])
            ->where('room_id', $this->roomId)
            ->orderByDesc('matched_at')
            ->get();

        $votes = MovieVote::where('room_id', $this->roomId)
            ->get(['room_participant_id', 'movie_id', 'decision']);

        $totalVotes = $votes->count();
        $totalYesVotes = $votes->where('decision', 'up')->count();
        $overallApproval = $totalVotes > 0 ? (int) round(($totalYesVotes / $totalVotes) * 100) : 0;

        $selectedMatch = $selectedMovie
            ? $matchedMovies->firstWhere('movie_id', $selectedMovie->id)
            : null;
        $finalMatchNumber = null;

        if ($selectedMatch && $selectedMatch->matched_at) {
            $finalMatchNumber = $matchedMovies
                ->filter(function ($match) use ($selectedMatch) {
                    return $match->matched_at
                        && $match->matched_at->lte($selectedMatch->matched_at);
                })
                ->count();
        }

        $roomDurationMinutes = ($room->started_at && $room->ended_at)
            ? (int) round($room->ended_at->diffInSeconds($room->started_at) / 60)
            : null;
        $roomDurationLabel = null;

        if ($roomDurationMinutes !== null) {
            if ($roomDurationMinutes >= 60) {
                $hours = intdiv($roomDurationMinutes, 60);
                $minutes = $roomDurationMinutes % 60;
                $roomDurationLabel = $minutes > 0
                    ? "{$hours} h {$minutes} m"
                    : "{$hours} h";
            } elseif ($roomDurationMinutes === 0) {
                $roomDurationLabel = 'less than 1 min';
            } else {
                $roomDurationLabel = "{$roomDurationMinutes} min";
            }
        }

        $hostId = $participants->firstWhere('is_host', true)?->id;
        $nonHostCount = $participants->where('is_host', false)->count();
        $selectedAudienceYes = 0;
        $selectedAudienceYesPercent = 0;

        if ($selectedMovie) {
            $selectedAudienceYes = $votes
                ->where('movie_id', $selectedMovie->id)
                ->where('decision', 'up')
                ->pluck('room_participant_id')
                ->unique()
                ->reject(fn ($participantId) => $participantId === $hostId)
                ->count();

            $selectedAudienceYesPercent = $nonHostCount > 0
                ? (int) round(($selectedAudienceYes / $nonHostCount) * 100)
                : 0;
        }

        $participantStats = $participants->map(function ($participant) use ($votes) {
            $participantVotes = $votes->where('room_participant_id', $participant->id);
            $yes = $participantVotes->where('decision', 'up')->count();
            $no = $participantVotes->where('decision', 'down')->count();
            $total = $yes + $no;

            return [
                'participant' => $participant,
                'yes' => $yes,
                'no' => $no,
                'total' => $total,
                'approval' => $total > 0 ? (int) round(($yes / $total) * 100) : 0,
            ];
        });

        $currentParticipantStats = $currentParticipant
            ? $participantStats->firstWhere('participant.id', $currentParticipant->id)
            : null;
        $nonHostStats = $participantStats;
        if ($currentParticipant) {
            $nonHostStats = $nonHostStats->reject(function ($stat) use ($currentParticipant) {
                return $stat['participant']->id === $currentParticipant->id;
            })->values();
        }

        $matchedMovieIds = $matchedMovies
            ->pluck('movie_id')
            ->filter()
            ->unique();

        $voteStats = $votes
            ->whereNotNull('movie_id')
            ->groupBy('movie_id')
            ->map(function ($items, $movieId) {
                $yes = $items->where('decision', 'up')->count();
                $no = $items->where('decision', 'down')->count();
                $total = $yes + $no;

                return [
                    'movie_id' => (int) $movieId,
                    'yes' => $yes,
                    'no' => $no,
                    'total' => $total,
                ];
            })
            ->values();

        $almostMatchedIds = $voteStats
            ->filter(function ($stat) use ($matchedMovieIds) {
                return $stat['yes'] > 0 && ! $matchedMovieIds->contains($stat['movie_id']);
            })
            ->sortByDesc(function ($stat) {
                return ($stat['yes'] * 1000) + $stat['total'];
            })
            ->take(4)
            ->pluck('movie_id')
            ->all();

        $mostDislikedIds = $voteStats
            ->filter(function ($stat) {
                return $stat['no'] > 0;
            })
            ->sortByDesc(function ($stat) {
                return ($stat['no'] * 1000) + $stat['total'];
            })
            ->take(4)
            ->pluck('movie_id')
            ->all();

        $otherMatchedMovies = $matchedMovies
            ->filter(function ($match) use ($selectedMovie) {
                return $match->movie && (! $selectedMovie || $match->movie_id !== $selectedMovie->id);
            })
            ->take(4)
            ->values();

        $almostMatchedMovies = $almostMatchedIds !== []
            ? Movie::whereIn('id', $almostMatchedIds)->get()->keyBy('id')
            : collect();
        $mostDislikedMovies = $mostDislikedIds !== []
            ? Movie::whereIn('id', $mostDislikedIds)->get()->keyBy('id')
            : collect();

        $voteStatsByMovieId = $voteStats->keyBy('movie_id');

        $genreCounts = $matchedMovies
            ->flatMap(fn ($match) => $match->movie?->genres?->pluck('name'))
            ->countBy()
            ->sortDesc()
            ->take(6);

        $actorCounts = $matchedMovies
            ->flatMap(fn ($match) => $match->movie?->actors?->pluck('name'))
            ->countBy()
            ->sortDesc()
            ->take(6);

        return view('livewire.room-stats', [
            'room' => $room,
            'participants' => $participants,
            'matchedMovies' => $matchedMovies,
            'selectedMovie' => $selectedMovie,
            'finalMatchNumber' => $finalMatchNumber,
            'roomDurationLabel' => $roomDurationLabel,
            'participantStats' => $participantStats,
            'currentParticipant' => $currentParticipant,
            'currentParticipantStats' => $currentParticipantStats,
            'nonHostStats' => $nonHostStats,
            'totalVotes' => $totalVotes,
            'totalYesVotes' => $totalYesVotes,
            'overallApproval' => $overallApproval,
            'genreCounts' => $genreCounts,
            'actorCounts' => $actorCounts,
            'nonHostCount' => $nonHostCount,
            'selectedAudienceYes' => $selectedAudienceYes,
            'selectedAudienceYesPercent' => $selectedAudienceYesPercent,
            'otherMatchedMovies' => $otherMatchedMovies,
            'almostMatchedIds' => $almostMatchedIds,
            'almostMatchedMovies' => $almostMatchedMovies,
            'mostDislikedIds' => $mostDislikedIds,
            'mostDislikedMovies' => $mostDislikedMovies,
            'voteStatsByMovieId' => $voteStatsByMovieId,
        ])->layout('components.layouts.marketing', ['title' => 'Stats for '.$this->roomCode]);
    }
}
