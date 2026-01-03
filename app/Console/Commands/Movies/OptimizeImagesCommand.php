<?php

namespace App\Console\Commands\Movies;

use Illuminate\Console\Command;

class OptimizeImagesCommand extends Command
{
    protected $signature = 'movies:optimize-images';

    protected $description = 'Optimize locally stored movie posters (TMDB uses URLs)';

    public function handle(): void
    {
        $this->info('Poster optimization skipped: TMDB uses remote URLs.');
    }
}
