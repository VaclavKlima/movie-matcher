<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Movie extends Model
{
    use Searchable;

    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'name',
        'original_title',
        'original_language',
        'poster_url',
        'backdrop_url',
        'country',
        'year',
        'duration',
        'description',
        'vote_average',
        'vote_count',
        'popularity',
        'tmdb_fetched_at',
    ];

    public function toSearchableArray(): array
    {
        $voteAverage = $this->vote_average !== null ? (float) $this->vote_average : null;
        $voteCount = $this->vote_count !== null ? (float) $this->vote_count : null;
        $popularity = $this->popularity !== null ? (float) $this->popularity : null;

        $ratingScoreMax = config('moviematcher.scoring.rating_max');
        $popularityScoreMax = config('moviematcher.scoring.popularity_max');
        $popularityLogMax = config('moviematcher.scoring.popularity_log_max');
        $voteCountThreshold = config('moviematcher.scoring.vote_count_threshold');
        $voteAverageMean = config('moviematcher.scoring.vote_average_mean');

        // Apply Bayesian weighted rating: (v/(v+m)) * R + (m/(v+m)) * C
        $weightedRating = ($voteAverage === null || $voteCount === null)
            ? $voteAverageMean
            : ($voteCount / ($voteCount + $voteCountThreshold)) * $voteAverage +
              ($voteCountThreshold / ($voteCount + $voteCountThreshold)) * $voteAverageMean;

        $ratingScore = $ratingScoreMax * ($weightedRating / 10.0);

        $popularityScore = $popularity === null
            ? 0.0
            : min(
                $popularityScoreMax,
                (log10($popularity + 1) / $popularityLogMax) * $popularityScoreMax
            );

        $popularityScore = $ratingScore + $popularityScore;

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

            'vote_average' => $voteAverage,
            'popularity' => $popularity,

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
