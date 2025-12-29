<?php

use App\Livewire\Home;
use App\Livewire\RoomJoin;
use App\Livewire\RoomLobby;
use App\Livewire\RoomMatch;
use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Volt\Volt;

Route::get('/', Home::class)->name('home');
Route::get('/rooms/create', RoomLobby::class)->name('rooms.create');
Route::get('/rooms/{code}', RoomLobby::class)->name('rooms.show');
Route::get('/rooms/{code}/join', RoomJoin::class)->name('rooms.join');
Route::get('/rooms/{code}/match', RoomMatch::class)->name('rooms.match');

Route::get('dashboard', function () {
    $movieStats = Cache::remember('dashboard.movie_stats.v2', now()->addMinutes(5), function () {
        $jobsTotal = null;
        $queueDriver = config('queue.default');
        $queueConfig = $queueDriver ? config("queue.connections.{$queueDriver}") : null;

        if (is_array($queueConfig) && ($queueConfig['driver'] ?? null) === 'redis') {
            $queueConnection = $queueConfig['connection'] ?? 'default';
            $queueNames = $queueConfig['queue'] ?? 'default';
            $queueNames = is_array($queueNames) ? $queueNames : explode(',', (string) $queueNames);
            $queueNames = array_filter(array_map('trim', $queueNames));
            $queueNames = $queueNames ?: ['default'];

            $jobsTotal = 0;
            $redis = Redis::connection($queueConnection);
            foreach ($queueNames as $queueName) {
                $queueKey = "queues:{$queueName}";
                $jobsTotal += (int) $redis->llen($queueKey);
                $jobsTotal += (int) $redis->zcard("{$queueKey}:reserved");
                $jobsTotal += (int) $redis->zcard("{$queueKey}:delayed");
            }
        } elseif (Schema::hasTable('jobs')) {
            $jobsTotal = (int) DB::table('jobs')->count();
        }

        $failedJobsTotal = Schema::hasTable('failed_jobs') ? (int) DB::table('failed_jobs')->count() : null;

        return [
            'movies_total' => Movie::count(),
            'votes_total' => MovieVote::count(),
            'votes_up' => MovieVote::where('decision', 'up')->count(),
            'votes_down' => MovieVote::where('decision', 'down')->count(),
            'movies_with_votes' => MovieVote::distinct('movie_id')->count('movie_id'),
            'jobs_total' => $jobsTotal,
            'failed_jobs_total' => $failedJobsTotal,
        ];
    });

    return view('dashboard', [
        'movieStats' => $movieStats,
    ]);
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
