<?php

namespace App\Actions;

use App\Data\RoomStats\MovieVoteStatsData;
use App\Data\RoomStats\ParticipantStatsData;
use App\Data\RoomStats\RoomStatsData;
use App\Models\Movie;
use App\Models\MovieVote;
use App\Models\Room;
use App\Models\RoomMovieMatch;
use App\Models\RoomParticipant;

class GetRoomStats
{
    public function handle(int $roomId, string $playerCookieId): RoomStatsData
    {
        $room = Room::findOrFail($roomId);
        $participants = RoomParticipant::where('room_id', $roomId)
            ->orderByDesc('is_host')
            ->orderBy('created_at')
            ->get();

        $currentParticipant = $participants->firstWhere('player_cookie_id', $playerCookieId);

        $selectedMovie = $room->matched_movie_id
            ? Movie::with(['genres', 'actors'])->find($room->matched_movie_id)
            : null;

        $matchedMovies = RoomMovieMatch::with(['movie.genres', 'movie.actors'])
            ->where('room_id', $roomId)
            ->orderByDesc('matched_at')
            ->get();

        $votes = MovieVote::where('room_id', $roomId)
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

            return new ParticipantStatsData(
                participant: $participant,
                yes: $yes,
                no: $no,
                total: $total,
                approval: $total > 0 ? (int) round(($yes / $total) * 100) : 0,
            );
        });

        $participantStatsData = ParticipantStatsData::collect($participantStats);
        $currentParticipantStats = $currentParticipant
            ? $participantStatsData->firstWhere('participant.id', $currentParticipant->id)
            : null;
        $nonHostStats = $participantStatsData;

        if ($currentParticipant) {
            $nonHostStats = $nonHostStats->reject(function ($stat) use ($currentParticipant) {
                return $stat->participant->id === $currentParticipant->id;
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

                return new MovieVoteStatsData(
                    movieId: (int) $movieId,
                    yes: $yes,
                    no: $no,
                    total: $total,
                );
            })
            ->values();

        $almostMatchedIds = $voteStats
            ->filter(function ($stat) use ($matchedMovieIds) {
                return $stat->yes > 0 && ! $matchedMovieIds->contains($stat->movieId);
            })
            ->sortByDesc(function ($stat) {
                return ($stat->yes * 1000) + $stat->total;
            })
            ->take(4)
            ->map(fn ($stat) => $stat->movieId)
            ->values()
            ->all();

        $mostDislikedIds = $voteStats
            ->filter(function ($stat) {
                return $stat->no > 0;
            })
            ->sortByDesc(function ($stat) {
                return ($stat->no * 1000) + $stat->total;
            })
            ->take(4)
            ->map(fn ($stat) => $stat->movieId)
            ->values()
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

        $voteStatsByMovieId = $voteStats->keyBy('movieId');

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

        return new RoomStatsData(
            room: $room,
            participants: $participants,
            matchedMovies: $matchedMovies,
            selectedMovie: $selectedMovie,
            finalMatchNumber: $finalMatchNumber,
            roomDurationLabel: $roomDurationLabel,
            participantStats: $participantStatsData,
            currentParticipant: $currentParticipant,
            currentParticipantStats: $currentParticipantStats,
            nonHostStats: $nonHostStats,
            totalVotes: $totalVotes,
            totalYesVotes: $totalYesVotes,
            overallApproval: $overallApproval,
            genreCounts: $genreCounts,
            actorCounts: $actorCounts,
            nonHostCount: $nonHostCount,
            selectedAudienceYes: $selectedAudienceYes,
            selectedAudienceYesPercent: $selectedAudienceYesPercent,
            otherMatchedMovies: $otherMatchedMovies,
            almostMatchedIds: $almostMatchedIds,
            almostMatchedMovies: $almostMatchedMovies,
            mostDislikedIds: $mostDislikedIds,
            mostDislikedMovies: $mostDislikedMovies,
            voteStatsByMovieId: $voteStatsByMovieId,
        );
    }
}
