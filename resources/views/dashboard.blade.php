<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400">
            <span>Overview</span>
            <span>Version {{ config('version.app') }}</span>
        </div>
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Movie Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Total movies</span>
                        <span class="font-semibold">{{ number_format($movieStats['movies_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Movies with votes</span>
                        <span class="font-semibold">{{ number_format($movieStats['movies_with_votes']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total votes</span>
                        <span class="font-semibold">{{ number_format($movieStats['votes_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Likes / Dislikes</span>
                        <span class="font-semibold">{{ number_format($movieStats['votes_up']) }} / {{ number_format($movieStats['votes_down']) }}</span>
                    </div>
                </div>
            </div>
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Queue Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Current jobs</span>
                        <span class="font-semibold">
                            {{ ($movieStats['jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['jobs_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Failed jobs</span>
                        <span class="font-semibold">
                            {{ ($movieStats['failed_jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['failed_jobs_total']) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Database Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Driver</span>
                        <span class="font-semibold">{{ $movieStats['db_driver'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Database</span>
                        <span class="font-semibold">{{ $movieStats['db_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total tables</span>
                        <span class="font-semibold">
                            {{ ($movieStats['db_tables_total'] ?? null) === null ? 'N/A' : number_format($movieStats['db_tables_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>DB size</span>
                        <span class="font-semibold">
                            {{ ($movieStats['db_size_bytes'] ?? null) === null ? 'N/A' : number_format($movieStats['db_size_bytes'] / 1024 / 1024, 2).' MB' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 text-sm shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                        Search (Meilisearch)
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-neutral-900 dark:text-neutral-100">
                    <div class="flex items-center justify-between">
                        <span>Status</span>
                        <span class="font-semibold">
                            @if ($searchStats['driver'] !== 'meilisearch')
                                Inactive
                            @elseif ($searchStats['unreachable'])
                                Unreachable
                            @else
                                Enabled
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Host</span>
                        <span class="font-semibold">{{ $searchStats['host'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Movie index</span>
                        <span class="font-semibold">
                            {{ $searchStats['full_movie_index'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Records</span>
                        <span class="font-semibold">
                            {{ $searchStats['documents_total'] === null ? 'N/A' : number_format($searchStats['documents_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Index size</span>
                        <span class="font-semibold">
                            {{ $searchStats['index_size_bytes'] === null ? 'N/A' : number_format($searchStats['index_size_bytes'] / 1024 / 1024, 2).' MB' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>API key</span>
                        <span class="font-semibold">{{ $searchStats['key_set'] ? 'Set' : 'Missing' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Queue</span>
                        <span class="font-semibold">
                            {{ $searchStats['queue_connection'] && $searchStats['queue_name']
                                ? $searchStats['queue_connection'] . ' / ' . $searchStats['queue_name']
                                : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>After commit</span>
                        <span class="font-semibold">{{ $searchStats['after_commit'] ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
