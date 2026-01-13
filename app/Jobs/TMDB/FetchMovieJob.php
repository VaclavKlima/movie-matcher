<?php

namespace App\Jobs\TMDB;

use App\Data\TMDB\IdMovie;
use App\Data\TMDB\TmdbMovie;
use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class FetchMovieJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly IdMovie $idMovie,
    ) {}

    public function handle(): void
    {
        // if the movie exists in the database, we don't need to fetch it again
        if (Movie::query()->where('tmdb_id', $this->idMovie->id)->exists()) {
            return;
        }

        $url = 'https://api.themoviedb.org/3/movie/{movie_id}';
        $url = str_replace('{movie_id}', $this->idMovie->id, $url);

        $response = Http::withHeader('Authorization', 'Bearer '.config('tmdb.read_access_token'))
            ->get($url, ['append_to_response' => 'credits'])
            ->throw();

        $tmdbMovie = TmdbMovie::from($response->json());
        if (! $tmdbMovie->title) {
            return;
        }
        $minPopularity = (float) config('tmdb.min_popularity', 0.5);
        if ($tmdbMovie->popularity < $minPopularity) {
            return;
        }

        $releaseYear = Carbon::parse($tmdbMovie->release_date)->year;

        $posterUrl = $this->buildImageUrl($tmdbMovie->poster_path, 'w500');
        $backdropUrl = $this->buildImageUrl($tmdbMovie->backdrop_path, 'w780');

        $country = $tmdbMovie->production_countries->toCollection()->first()?->name;

        $movie = Movie::updateOrCreate(
            ['tmdb_id' => $tmdbMovie->id],
            [
                'imdb_id' => $tmdbMovie->imdb_id,
                'name' => $tmdbMovie->title,
                'original_title' => $tmdbMovie->original_title,
                'original_language' => $tmdbMovie->original_language,
                'poster_url' => $posterUrl,
                'backdrop_url' => $backdropUrl,
                'country' => $country,
                'year' => $releaseYear,
                'duration' => $tmdbMovie->runtime ? $tmdbMovie->runtime.' min' : null,
                'description' => $tmdbMovie->overview,
                'vote_average' => $tmdbMovie->vote_average,
                'vote_count' => $tmdbMovie->vote_count,
                'popularity' => $tmdbMovie->popularity,
                'tmdb_fetched_at' => Carbon::now(),
            ]
        );

        $genreIds = $tmdbMovie->genres
            ->toCollection()
            ->map(function ($genre) {
                return Genre::updateOrCreate(
                    ['tmdb_id' => $genre->id],
                    ['name' => $genre->name]
                )->id;
            })
            ->all();

        $movie->genres()->sync($genreIds);

        $cast = collect($response->json('credits.cast', []))
            ->filter(fn ($actor) => is_array($actor) && ! empty($actor['id']) && ! empty($actor['name']))
            ->take(5);

        $actorIds = [];
        foreach ($cast as $actorData) {
            $actorIds[] = Actor::updateOrCreate(
                ['tmdb_id' => $actorData['id']],
                ['name' => $actorData['name']]
            )->id;
        }

        $movie->actors()->sync($actorIds);
    }

    private function buildImageUrl(?string $path, string $size): ?string
    {
        if (! $path) {
            return null;
        }

        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }
}
