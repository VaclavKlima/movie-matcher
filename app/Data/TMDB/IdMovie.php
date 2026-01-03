<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Data;

class IdMovie extends Data
{
    public function __construct(
        public bool $adult,
        public int $id,
        public string $original_title,
        public float $popularity,
        public bool $video,
    ) {}
}
