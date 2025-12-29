<?php

namespace App\Jobs;

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
        $html = Http::get($this->url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $name = $xpath->query('//div[@class="film-header-name"]/h1')->item(0)->nodeValue;
        $name = trim($name);

        $genres = $xpath->query('//div[@class="genres"]')->item(0)->textContent;

        $genres = explode(' / ', $genres);

        $genres = array_map(function ($genre) {
            return trim($genre);
        }, $genres);

        $filmPosterUrl = $xpath->query('//div[@class="film-posters"]//a/img')->item(0)?->getAttribute('src');

        $origin = $xpath->query('//div[@class="film-info-content"]/div[@class="origin"]')->item(0)->textContent;

        [$country, $year, $duration] = explode(',', $origin);

        $description = $xpath->query('//div[@class="plot-full"]')->item(0)?->textContent ?? '';

        $movie = Movie::firstOrCreate([
            'name' => $name,
        ],[
            'name' => $name,
            'poster_image' => $filmPosterUrl ? base64_encode(file_get_contents('https:' . $filmPosterUrl)) : null,
            'country' => trim($country),
            'year' => trim($year),
            'duration' => trim(preg_replace('/\s+/', ' ', $duration)),
            'description' => trim($description),
        ]);

        $movie->genres()->attach(
            array_map(function ($genre) {
                return Genre::firstOrCreate(['name' => $genre])->id;
            }, $genres)
        );
    }
}
