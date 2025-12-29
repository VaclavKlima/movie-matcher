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
    $movieStats = Cache::remember('dashboard.movie_stats.v4', now()->addMinutes(5), function () {
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
        $dbDriver = DB::connection()->getDriverName();
        $dbName = DB::connection()->getDatabaseName();
        $tablesTotal = null;
        $dbSizeBytes = null;

        switch ($dbDriver) {
            case 'mysql':
                $tablesTotal = (int) (DB::selectOne(
                    'select count(*) as total from information_schema.tables where table_schema = database() and table_type = "BASE TABLE"'
                )->total ?? 0);
                $dbSizeBytes = (int) (DB::selectOne(
                    'select sum(data_length + index_length) as total from information_schema.tables where table_schema = database()'
                )->total ?? 0);
                break;
            case 'pgsql':
                $tablesTotal = (int) (DB::selectOne(
                    "select count(*) as total from information_schema.tables where table_schema = 'public' and table_type = 'BASE TABLE'"
                )->total ?? 0);
                $dbSizeBytes = (int) (DB::selectOne(
                    'select pg_database_size(current_database()) as total'
                )->total ?? 0);
                break;
            case 'sqlite':
                $tablesTotal = (int) (DB::selectOne(
                    "select count(*) as total from sqlite_master where type = 'table' and name not like 'sqlite_%'"
                )->total ?? 0);
                $pageCount = (int) (DB::selectOne('pragma page_count')->page_count ?? 0);
                $pageSize = (int) (DB::selectOne('pragma page_size')->page_size ?? 0);
                $dbSizeBytes = $pageCount && $pageSize ? $pageCount * $pageSize : null;
                break;
            case 'sqlsrv':
                $tablesTotal = (int) (DB::selectOne(
                    'select count(*) as total from information_schema.tables where table_type = \'BASE TABLE\''
                )->total ?? 0);
                $dbSizeBytes = (int) (DB::selectOne(
                    'select sum(reserved_page_count) * 8 * 1024 as total from sys.dm_db_partition_stats'
                )->total ?? 0);
                break;
        }

        return [
            'movies_total' => Movie::count(),
            'votes_total' => MovieVote::count(),
            'votes_up' => MovieVote::where('decision', 'up')->count(),
            'votes_down' => MovieVote::where('decision', 'down')->count(),
            'movies_with_votes' => MovieVote::distinct('movie_id')->count('movie_id'),
            'jobs_total' => $jobsTotal,
            'failed_jobs_total' => $failedJobsTotal,
            'db_driver' => $dbDriver,
            'db_name' => $dbName,
            'db_tables_total' => $tablesTotal,
            'db_size_bytes' => $dbSizeBytes,
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
