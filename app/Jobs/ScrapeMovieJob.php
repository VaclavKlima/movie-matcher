<?php

namespace App\Jobs;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ScrapeMovieJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $url,
    )
    {
    }

    public function handle()
    {
        $xpath = $this->loadXPath($this->url);

        $name = $this->extractName($xpath);
        if ($name === '') {
            return;
        }

        if (Movie::where('name', $name)->exists()) {
            return;
        }

        $genres = $this->extractGenres($xpath);
        $actors = $this->extractActors($xpath);
        $filmPosterUrl = $this->extractPosterUrl($xpath);

        if ($filmPosterUrl === null) {
            return;
        }

        $averageRating = $this->extractAverageRating($xpath);
        [$filmRank, $filmPopularityRank] = $this->extractRanking($xpath);
        [$country, $year, $duration] = $this->extractOriginParts($xpath);
        $description = $this->extractDescription($xpath);

        $movie = Movie::create([
            'name' => $name,
            'poster_image' => base64_encode(file_get_contents('https:' . $filmPosterUrl)),
            'country' => trim($country),
            'year' => trim($year),
            'duration' => trim(preg_replace('/\s+/', ' ', $duration)),
            'description' => trim($description),
            'average_rating' => $averageRating,
            'film_rank' => $filmRank,
            'film_popularity_rank' => $filmPopularityRank,
        ]);

        $this->attachGenres($movie, $genres);
        $this->attachActors($movie, $actors);
    }

    private function loadXPath(string $url): \DOMXPath
    {
        $html = Http::get($url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        return new \DOMXPath($dom);
    }

    private function extractName(\DOMXPath $xpath): string
    {
        $name = $xpath->query('//div[@class="film-header-name"]/h1')->item(0)->nodeValue;

        return trim($name);
    }

    private function extractGenres(\DOMXPath $xpath): array
    {
        $genres = $xpath->query('//div[@class="genres"]')->item(0)->textContent;
        $genres = explode(' / ', $genres);

        return array_map(function ($genre) {
            return trim($genre);
        }, $genres);
    }

    private function extractActors(\DOMXPath $xpath): array
    {
        $actorNodes = $xpath->query('//div[@id="creators"]/div[h4[contains(normalize-space(.), "Hraj")]]//a');
        $actors = [];
        foreach ($actorNodes as $actorNode) {
            $actorName = trim($actorNode?->textContent ?? '');
            if ($actorName === '') {
                continue;
            }

            $actors[] = $actorName;
            if (count($actors) >= 5) {
                break;
            }
        }

        return $actors;
    }

    private function extractPosterUrl(\DOMXPath $xpath): ?string
    {
        return $xpath->query('//div[@class="film-posters"]//a/img')->item(0)?->getAttribute('src');
    }

    private function extractAverageRating(\DOMXPath $xpath): ?int
    {
        $averageRatingText = trim($xpath->query('//div[@class="film-rating-average"]')->item(0)?->textContent ?? '');
        if ($averageRatingText === '' || $averageRatingText === '?%') {
            return null;
        }

        if (preg_match('/(\d{1,3})%/', $averageRatingText, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/\d{1,3}/', $averageRatingText, $matches)) {
            return (int) $matches[0];
        }

        return null;
    }

    private function extractRanking(\DOMXPath $xpath): array
    {
        $filmRank = null;
        $filmPopularityRank = null;
        $rankingLinks = $xpath->query('//div[@class="film-ranking"]/a');
        foreach ($rankingLinks as $rankingLink) {
            $rankingHref = $rankingLink?->getAttribute('href') ?? '';
            $rankingText = trim($rankingLink?->textContent ?? '');
            if ($rankingText === '') {
                continue;
            }

            if ($filmRank === null && str_contains($rankingHref, '/zebricky/filmy/nejlepsi/')) {
                if (preg_match('/\b(\d{1,7})\b/', $rankingText, $matches)) {
                    $filmRank = (int) $matches[1];
                }
                continue;
            }

            if ($filmPopularityRank === null && str_contains($rankingHref, '/zebricky/filmy/nejoblibenejsi/')) {
                if (preg_match('/\b(\d{1,7})\b/', $rankingText, $matches)) {
                    $filmPopularityRank = (int) $matches[1];
                }
            }

            if ($filmRank !== null && $filmPopularityRank !== null) {
                break;
            }
        }

        return [$filmRank, $filmPopularityRank];
    }

    private function extractOriginParts(\DOMXPath $xpath): array
    {
        $origin = $xpath->query('//div[@class="film-info-content"]/div[@class="origin"]')->item(0)->textContent;
        [$country, $year, $duration] = explode(',', $origin);

        return [$country, $year, $duration];
    }

    private function extractDescription(\DOMXPath $xpath): string
    {
        return $xpath->query('//div[@class="plot-full"]')->item(0)?->textContent ?? '';
    }

    private function attachGenres(Movie $movie, array $genres): void
    {
        $genres = array_values(array_filter($genres));
        if ($genres === []) {
            return;
        }

        $existingGenres = Genre::whereIn('name', $genres)->get(['id', 'name']);
        $existingByName = $existingGenres->keyBy('name');

        $missingGenres = array_values(array_diff($genres, $existingByName->keys()->all()));
        if ($missingGenres !== []) {
            Genre::insert(array_map(function ($genre) {
                return ['name' => $genre];
            }, $missingGenres));

            $existingGenres = Genre::whereIn('name', $genres)->get(['id', 'name']);
            $existingByName = $existingGenres->keyBy('name');
        }

        $movie->genres()->attach($existingByName->pluck('id')->all());
    }

    private function attachActors(Movie $movie, array $actors): void
    {
        if ($actors === []) {
            return;
        }

        $actors = array_values(array_unique($actors));
        $existingActors = Actor::whereIn('name', $actors)->get(['id', 'name']);
        $existingByName = $existingActors->keyBy('name');

        $missingActors = array_values(array_diff($actors, $existingByName->keys()->all()));
        if ($missingActors !== []) {
            Actor::insert(array_map(function ($actor) {
                return ['name' => $actor];
            }, $missingActors));

            $existingActors = Actor::whereIn('name', $actors)->get(['id', 'name']);
            $existingByName = $existingActors->keyBy('name');
        }

        $movie->actors()->attach($existingByName->pluck('id')->all());
    }
}
