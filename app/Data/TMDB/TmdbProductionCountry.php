<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class TmdbProductionCountry extends Data
{
    public function __construct(
        public string $iso_3166_1,
        public string $name,
    ) {}
}
