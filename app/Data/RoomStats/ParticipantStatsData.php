<?php

namespace App\Data\RoomStats;

use App\Models\RoomParticipant;
use Spatie\LaravelData\Data;

class ParticipantStatsData extends Data
{
    public function __construct(
        public RoomParticipant $participant,
        public int $yes,
        public int $no,
        public int $total,
        public int $approval,
    ) {
    }
}
