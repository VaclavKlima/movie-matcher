<?php

namespace App\Data\RoomStats;

use Spatie\LaravelData\Data;

class MovieVoteStatsData extends Data
{
    public function __construct(
        public int $movieId,
        public int $yes,
        public int $no,
        public int $total,
    ) {
    }
}
