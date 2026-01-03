<?php

namespace App\Console\Commands;

use App\Data\TMDB\IdMovie;
use App\Jobs\TMDB\FetchMovieJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TmdbScrapeMoviesCommand extends Command
{
    protected $signature = 'tmdb:scrape-movies';

    protected $description = 'Download TMDB movie ids export';

    public function handle(): void
    {
        $url = 'https://files.tmdb.org/p/exports/movie_ids_:month_:day_:year.json.gz';
        $apiKey = config('tmdb.read_access_token');
        $date = now()->subDays(7);

        $url = str_replace([
            ':month', ':day', ':year',
        ], [
            $date->month, $date->day, $date->year,
        ], $url);

        $response = Http::withHeader('Authorization', "Bearer {$apiKey}")
            ->withOptions(['stream' => true])
            ->get($url)
            ->throw();

        $filePath = Storage::disk('local')->path('imdb_movie_ids.json');
        $contentLength = (int) $response->header('Content-Length', 0);
        $progressBar = $this->output->createProgressBar($contentLength ?: 0);
        $progressBar->start();

        $body = $response->toPsrResponse()->getBody();
        $inflate = inflate_init(ZLIB_ENCODING_GZIP);
        $handle = fopen($filePath, 'wb');
        if ($handle === false) {
            $this->error('Unable to write movie ids to storage.');

            return;
        }

        while (! $body->eof()) {
            $chunk = $body->read(1024 * 1024);
            if ($chunk === '') {
                break;
            }

            $decoded = inflate_add(
                $inflate,
                $chunk,
                $body->eof() ? ZLIB_FINISH : ZLIB_SYNC_FLUSH
            );

            fwrite($handle, $decoded);
            $progressBar->advance($contentLength > 0 ? strlen($chunk) : 1);
        }

        fclose($handle);
        $progressBar->finish();
        $this->newLine();
        $this->info("Saved TMDB movie ids to {$filePath}");

        $movies = collect();
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            $this->error('Unable to read movie ids from storage.');

            return;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $movie = json_decode($line, true);
            if (is_array($movie)) {
                $movies->push($movie);
            }
        }

        fclose($handle);

        $minPopularity = (float) config('tmdb.min_popularity', 0.5);

        $movies = $movies
            ->filter(fn ($movie) => ($movie['popularity'] ?? 0) >= $minPopularity)
            ->sortByDesc('popularity')
            ->values();

        $this->info("Loaded {$movies->count()} movies sorted by popularity.");

        $progressBar = $this->output->createProgressBar($movies->count());
        $progressBar->start();

        foreach ($movies as $movie) {
            FetchMovieJob::dispatch(IdMovie::from($movie));
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->newLine();
        $this->info('All movies queued for download.');
    }
}
