<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(): View
    {
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
                $jobsTotal = DB::table('jobs')->count();
            }

            $failedJobsTotal = Schema::hasTable('failed_jobs') ? (int) DB::table('failed_jobs')->count() : null;
            $dbDriver = DB::connection()->getDriverName();
            $dbName = DB::connection()->getDatabaseName();
            $tablesTotal = (int) (DB::selectOne(
                'select count(*) as total from information_schema.tables where table_schema = database() and table_type = "BASE TABLE"'
            )->total ?? 0);
            $dbSizeBytes = (float) DB::selectOne('
                SELECT
                    table_schema AS database_name,
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
                GROUP BY table_schema
            ')->size_mb * 1024 * 1024;

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
    }
}
