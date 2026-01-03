<?php

namespace App\Console\Commands;

use App\Data\TMDB\IdMovie;
use App\Jobs\TMDB\FetchMovieJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TmdbScrapeMoviesCommand extends Command
{
    protected $signature = 'tmdb:scrape-movies';

    protected $description = 'Download TMDB movie ids export';

    public function handle(): void
    {
        $url = 'https://files.tmdb.org/p/exports/movie_ids_:month_:day_:year.json.gz';
        $apiKey = config('tmdb.read_access_token');
        $date = now()->subDays(30);

        $url = str_replace([
            ':month', ':day', ':year',
        ], [
            str_pad($date->month, 2, '0', STR_PAD_LEFT),
            str_pad($date->day, 2, '0', STR_PAD_LEFT),
            $date->year,
        ], $url);

        $this->info('Downloading TMDB movie ids export...');
        $this->info("URL: {$url}");
        $this->info("Date: {$date->toDateString()}");

        try {
            $response = Http::withHeader('Authorization', "Bearer {$apiKey}")
                ->timeout(300)
                ->withOptions(['stream' => true])
                ->get($url);

            $this->info("HTTP Status: {$response->status()}");

            if (!$response->successful()) {
                $this->error("Failed to download file. HTTP Status: {$response->status()}");
                $this->error("Response: {$response->body()}");
                return;
            }

            $body = $response->toPsrResponse()->getBody();
            $inflate = inflate_init(ZLIB_ENCODING_GZIP);
            $movies = collect();
            $buffer = '';
            $bytesDownloaded = 0;
            $chunkCount = 0;

            $this->info('Starting stream download...');

            while (! $body->eof()) {
                $chunk = $body->read(1024 * 1024);
                if ($chunk === '') {
                    break;
                }

                $bytesDownloaded += strlen($chunk);
                $chunkCount++;

                if ($chunkCount % 10 === 0) {
                    $this->info("Downloaded: " . round($bytesDownloaded / 1024 / 1024, 2) . " MB ({$movies->count()} movies parsed)");
                }

                $decoded = inflate_add(
                    $inflate,
                    $chunk,
                    $body->eof() ? ZLIB_FINISH : ZLIB_SYNC_FLUSH
                );
                if ($decoded === false) {
                    $this->error('Failed to decode TMDB export stream.');
                    return;
                }

                $buffer .= $decoded;
                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $newlinePos));
                    $buffer = substr($buffer, $newlinePos + 1);
                    if ($line === '') {
                        continue;
                    }

                    $movie = json_decode($line, true);
                    if (is_array($movie)) {
                        $movies->push($movie);
                    }
                }
            }

            $this->info('Download complete.');
            $line = trim($buffer);
            if ($line !== '') {
                $movie = json_decode($line, true);
                if (is_array($movie)) {
                    $movies->push($movie);
                }
            }
            $this->info('Loaded movie ids into memory.');
        } catch (\Exception $e) {
            $this->error('Error downloading or processing TMDB export:');
            $this->error($e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return;
        }

        $minPopularity = (float) config('tmdb.min_popularity', 0.5);

        $movies = $movies
            ->filter(fn ($movie) => ($movie['popularity'] ?? 0) >= $minPopularity)
            ->sortByDesc('popularity')
            ->values();

        $this->info("Loaded {$movies->count()} movies sorted by popularity.");
        $this->info('Dispatching jobs for movie details...');

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
