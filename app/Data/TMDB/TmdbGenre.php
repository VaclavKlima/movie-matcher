<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class TmdbGenre extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}
