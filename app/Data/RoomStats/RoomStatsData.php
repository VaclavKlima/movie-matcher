<?php

namespace App\Data\RoomStats;

use App\Models\Movie;
use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class RoomStatsData extends Data
{
    public function __construct(
        public Room $room,
        public Collection $participants,
        public Collection $matchedMovies,
        public ?Movie $selectedMovie,
        public ?int $finalMatchNumber,
        public ?string $roomDurationLabel,
        public DataCollection $participantStats,
        public ?RoomParticipant $currentParticipant,
        public ?ParticipantStatsData $currentParticipantStats,
        public DataCollection $nonHostStats,
        public int $totalVotes,
        public int $totalYesVotes,
        public int $overallApproval,
        public Collection $genreCounts,
        public Collection $actorCounts,
        public int $nonHostCount,
        public int $selectedAudienceYes,
        public int $selectedAudienceYesPercent,
        public Collection $otherMatchedMovies,
        public array $almostMatchedIds,
        public Collection $almostMatchedMovies,
        public array $mostDislikedIds,
        public Collection $mostDislikedMovies,
        public Collection $voteStatsByMovieId,
    ) {
    }
}
