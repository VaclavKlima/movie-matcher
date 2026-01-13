<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rooms:end-inactive')
    ->everyFifteenMinutes()
    ->environments(['production']);

Schedule::command('telescope:prune --hours=720')->daily();

Schedule::command('tmdb:queue-oldest-movies')
    ->dailyAt('1:00');
