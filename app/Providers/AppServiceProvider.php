<?php

namespace App\Providers;

use App\Models\Movie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Meilisearch\Client;


class AppServiceProvider extends ServiceProvider
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
        Gate::define('viewPulse', fn ($user) => (bool) $user->is_admin);


        if (! config('scout.meilisearch.host')) {
            return;
        }

        try {
            $client = new Client(
                config('scout.meilisearch.host'),
                config('scout.meilisearch.key')
            );

            $index = $client->index((new Movie)->searchableAs());

            $index->updateFilterableAttributes([
                'genre_ids',
                'actor_ids',
                'year',
            ]);

            $index->updateSortableAttributes([
                'popularity_score',
            ]);
        } catch (\Throwable $e) {
            // avoid crashing app if Meili is down during deploy
            report($e);
        }
    }
}
