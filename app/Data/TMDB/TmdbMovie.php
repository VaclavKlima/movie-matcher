<?php

namespace App\Data\TMDB;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TmdbMovie extends Data
{
    public function __construct(
        public bool $adult,
        public ?string $backdrop_path,
        public ?TmdbCollection $belongs_to_collection,
        public int $budget,
        #[DataCollectionOf(TmdbGenre::class)]
        public DataCollection $genres,
        public ?string $homepage,
        public int $id,
        public ?string $imdb_id,
        public string $original_language,
        public string $original_title,
        public string $overview,
        public float $popularity,
        public ?string $poster_path,
        #[DataCollectionOf(TmdbProductionCompany::class)]
        public DataCollection $production_companies,
        #[DataCollectionOf(TmdbProductionCountry::class)]
        public DataCollection $production_countries,
        public string $release_date,
        public int $revenue,
        public int $runtime,
        #[DataCollectionOf(TmdbSpokenLanguage::class)]
        public DataCollection $spoken_languages,
        public string $status,
        public ?string $tagline,
        public string $title,
        public bool $video,
        public float $vote_average,
        public int $vote_count,
    ) {
    }
}
