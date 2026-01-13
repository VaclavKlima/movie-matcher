<?php

namespace App\Console\Commands;

use App\Data\TMDB\IdMovie;
use App\Jobs\TMDB\FetchMovieJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Laravel\Telescope\Telescope;

class TmdbScrapeMoviesCommand extends Command
{
    protected $signature = 'tmdb:scrape-movies';

    protected $description = 'Download TMDB movie ids export';

    public function handle(): void
    {
        ini_set('memory_limit', '250M');
        gc_enable();

        Telescope::stopRecording();

        $exportUrlTemplate = 'https://files.tmdb.org/p/exports/movie_ids_:month_:day_:year.json.gz';
        $apiKey = (string) config('tmdb.read_access_token');
        $date = now()->subDays(30);

        $exportUrl = str_replace(
            [':month', ':day', ':year'],
            [
                str_pad((string) $date->month, 2, '0', STR_PAD_LEFT),
                str_pad((string) $date->day, 2, '0', STR_PAD_LEFT),
                (string) $date->year,
            ],
            $exportUrlTemplate
        );

        $this->info('Downloading TMDB movie ids export...');
        $this->info("URL: {$exportUrl}");
        $this->info("Date: {$date->toDateString()}");
        $this->info('Queue connection (config queue.default): '.(string) config('queue.default'));

        $temporaryFilePath = tempnam(sys_get_temp_dir(), 'tmdb_export_');
        if (! $temporaryFilePath) {
            $this->error('Failed to create a temp file for the TMDB export.');

            return;
        }

        $gzipHandle = null;

        try {
            $response = Http::withHeader('Authorization', "Bearer {$apiKey}")
                ->timeout(300)
                ->withOptions(['sink' => $temporaryFilePath])
                ->get($exportUrl);

            $this->info("HTTP Status: {$response->status()}");

            if (! $response->successful()) {
                $this->error("Failed to download file. HTTP Status: {$response->status()}");
                $this->error("Response: {$response->body()}");

                return;
            }

            $downloadedBytes = is_file($temporaryFilePath) ? filesize($temporaryFilePath) : 0;
            $this->info('Downloaded: '.round($downloadedBytes / 1024 / 1024, 2).' MB');
            $this->info('Processing export file (streaming from disk)...');

            $gzipHandle = gzopen($temporaryFilePath, 'rb');
            if ($gzipHandle === false) {
                $this->error('Failed to open TMDB export gzip file.');

                return;
            }

            $minimumPopularity = (float) config('tmdb.min_popularity', 0.4);

            $parsedLineCount = 0;
            $queuedMovieCount = 0;

            $maximumLineBytesToRead = 64 * 1024;

            while (! gzeof($gzipHandle)) {
                $line = gzgets($gzipHandle, $maximumLineBytesToRead);
                if ($line === false) {
                    break;
                }

                $parsedLineCount++;

                $line = rtrim($line, "\r\n");
                if ($line === '') {
                    continue;
                }

                $movie = json_decode($line);

                if (! is_object($movie) || ! isset($movie->id)) {
                    unset($movie, $line);
                    continue;
                }

                $popularity = (float) ($movie->popularity ?? 0);

                if ($popularity >= $minimumPopularity) {
                    $adult = (bool) ($movie->adult ?? false);
                    $id = (int) $movie->id;

                    $originalTitleValue = $movie->original_title ?? '';
                    $originalTitle = is_string($originalTitleValue) ? $originalTitleValue : '';

                    $video = (bool) ($movie->video ?? false);

                    // Important: bypass Spatie Data::from() to avoid Collection allocations.
                    $idMovie = new IdMovie($adult, $id, $originalTitle, $popularity, $video);

                    $pendingDispatch = FetchMovieJob::dispatch($idMovie)
                        ->onConnection('redis')
                        ->onQueue('tmdb');

                    unset($pendingDispatch, $idMovie);

                    $queuedMovieCount++;
                }

                unset($movie, $line);

                if ($parsedLineCount % 200000 === 0) {
                    $this->info("Parsed {$parsedLineCount} lines, queued {$queuedMovieCount} movies");
                    gc_collect_cycles();
                }
            }

            $this->info("Done. Parsed {$parsedLineCount} lines.");
            $this->info("Queued {$queuedMovieCount} movies (min_popularity={$minimumPopularity}).");
        } catch (\Throwable $e) {
            $this->error('Error downloading or processing TMDB export:');
            $this->error($e->getMessage());
            $this->error('Stack trace: '.$e->getTraceAsString());

            return;
        } finally {
            if (is_resource($gzipHandle)) {
                gzclose($gzipHandle);
            }

            if (is_file($temporaryFilePath)) {
                @unlink($temporaryFilePath);
            }
        }

        $this->info('All movies queued for download.');
    }
}
