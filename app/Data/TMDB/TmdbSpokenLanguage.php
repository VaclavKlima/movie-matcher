<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class TmdbSpokenLanguage extends Data
{
    public function __construct(
        public string $english_name,
        public string $iso_639_1,
        public string $name,
    ) {
    }
}
