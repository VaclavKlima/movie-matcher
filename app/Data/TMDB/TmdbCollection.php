<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class TmdbCollection extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $poster_path,
        public ?string $backdrop_path,
    ) {
    }
}
