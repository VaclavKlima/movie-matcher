<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Movie extends Model
{
    use Searchable;

    protected $fillable = [
        'name',
        'poster_image',
        'country',
        'year',
        'duration',
        'description',
        'average_rating',
        'film_rank',
        'film_popularity_rank',
    ];

    public function toSearchableArray(): array
    {
        $averageRating = $this->average_rating !== null ? (float) $this->average_rating : null;
        $filmRank = $this->film_rank !== null ? (int) $this->film_rank : null;
        $filmPopularityRank = $this->film_popularity_rank !== null ? (int) $this->film_popularity_rank : null;

        // Same constants as in SuggestMovie (keep in sync)
        $ratingScoreMax = 5.0;
        $rankScoreMax = 5.0;
        $rankScoreRange = 1000.0;

        $ratingScore = $averageRating === null
            ? 0.0
            : $ratingScoreMax * ($averageRating / 100.0);

        $filmRankScore = $filmRank === null
            ? 0.0
            : $rankScoreMax * (1.0 - (($filmRank - 1) / $rankScoreRange));

        $filmPopularityScore = $filmPopularityRank === null
            ? 0.0
            : $rankScoreMax * (1.0 - (($filmPopularityRank - 1) / $rankScoreRange));

        $popularityScore = $ratingScore + $filmRankScore + $filmPopularityScore;

        $actorIds = $this->relationLoaded('actors')
            ? $this->actors->modelKeys()
            : $this->actors()->pluck('id')->all();

        $genreIds = $this->relationLoaded('genres')
            ? $this->genres->modelKeys()
            : $this->genres()->pluck('id')->all();

        return [
            'id' => (int) $this->id,
            'year' => $this->year !== null ? (int) $this->year : null,

            'actor_ids' => array_values(array_map('intval', $actorIds)),
            'genre_ids' => array_values(array_map('intval', $genreIds)),

            'average_rating' => $averageRating,
            'film_rank' => $filmRank,
            'film_popularity_rank' => $filmPopularityRank,

            'popularity_score' => $popularityScore,
        ];
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'movie_actor');
    }
}
