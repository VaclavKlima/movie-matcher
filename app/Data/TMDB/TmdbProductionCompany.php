<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class TmdbProductionCompany extends Data
{
    public function __construct(
        public int $id,
        public ?string $logo_path,
        public string $name,
        public string $origin_country,
    ) {}
}
