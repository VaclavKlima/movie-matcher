<?php

namespace App\Console\Commands;

use App\Jobs\TMDB\RefreshMovieJob;
use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class QueueOldestTmdbMoviesCommand extends Command
{
    protected $signature = 'tmdb:queue-oldest-movies
        {--count= : Number of movies to queue}
        {--interval= : Delay in seconds between queued jobs}';

    protected $description = 'Queue TMDB refresh jobs for the oldest fetched movies';

    public function handle(): int
    {
        $count = $this->option('count');
        $count = $count !== null ? (int) $count : (int) config('tmdb.daily_refresh_count', 17280);

        if ($count <= 0) {
            $this->error('Count must be greater than zero.');

            return self::FAILURE;
        }

        $interval = $this->option('interval');
        $interval = $interval !== null ? (int) $interval : (int) config('tmdb.refresh_interval_seconds', 5);
        $interval = max(1, $interval);

        $movies = Movie::query()
            ->whereNotNull('tmdb_id')
            ->orderByRaw('tmdb_fetched_at is null desc')
            ->orderBy('tmdb_fetched_at')
            ->limit($count)
            ->get(['tmdb_id'])
            ->shuffle()
            ->values();

        if ($movies->isEmpty()) {
            $this->info('No movies found to queue.');

            return self::SUCCESS;
        }

        $queued = 0;
        $connection = (string) config('queue.default');

        foreach ($movies as $index => $movie) {
            $delaySeconds = $index * $interval;
            $pendingDispatch = RefreshMovieJob::dispatch((int) $movie->tmdb_id)
                ->delay(Carbon::now()->addSeconds($delaySeconds))
                ->onConnection($connection);

            unset($pendingDispatch);

            $queued++;
        }

        $this->info("Queued {$queued} movie refresh jobs.");

        return self::SUCCESS;
    }
}
