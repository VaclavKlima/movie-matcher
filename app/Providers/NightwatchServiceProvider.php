<?php

namespace App\Providers;

use App\Jobs\ScrapeMovieJob;
use App\Models\Movie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Nightwatch\Facades\Nightwatch;
use Laravel\Nightwatch\Records\QueuedJob;
use Laravel\Scout\Jobs\MakeSearchable;
use Meilisearch\Client;

class NightwatchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Nightwatch::rejectQueuedJobs(function (QueuedJob $job) {
            return in_array($job->name, [
                ScrapeMovieJob::class,
                MakeSearchable::class,
            ]);
        });
    }
}
