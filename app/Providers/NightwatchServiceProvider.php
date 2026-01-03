<?php

namespace App\Providers;

use App\Jobs\TMDB\FetchMovieJob;
use Illuminate\Support\ServiceProvider;
use Laravel\Nightwatch\Facades\Nightwatch;
use Laravel\Nightwatch\Records\QueuedJob;
use Laravel\Scout\Jobs\MakeSearchable;

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
                FetchMovieJob::class,
                MakeSearchable::class,
            ]);
        });
    }
}
