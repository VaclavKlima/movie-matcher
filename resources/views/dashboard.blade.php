<x-layouts.app :title="__('Dashboard')">
    <div class="relative flex h-full w-full flex-1 flex-col gap-6 overflow-hidden p-6 sm:p-8">
        <div class="relative flex flex-wrap items-center justify-between gap-3 text-xs uppercase tracking-[0.2em] text-amber-200/80">
            <span class="flex items-center gap-2 text-amber-100">
                🎬 NOW SHOWING
            </span>
            <span class="text-amber-200/70">Version {{ config('version.app') }}</span>
        </div>

        <div class="relative grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                        🎞 Movie Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2">
                    <div class="flex items-center justify-between">
                        <span>Total movies</span>
                        <span class="font-semibold text-amber-100">{{ number_format($movieStats['movies_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Movies with votes</span>
                        <span class="font-semibold text-amber-100">{{ number_format($movieStats['movies_with_votes']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total votes</span>
                        <span class="font-semibold text-amber-100">{{ number_format($movieStats['votes_total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Likes / Dislikes</span>
                        <span class="font-semibold text-amber-100">{{ number_format($movieStats['votes_up']) }} / {{ number_format($movieStats['votes_down']) }}</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                        🍿 Queue Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2">
                    <div class="flex items-center justify-between">
                        <span>Current jobs</span>
                        <span class="font-semibold text-amber-100">
                            {{ ($movieStats['jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['jobs_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Failed jobs</span>
                        <span class="font-semibold text-amber-100">
                            {{ ($movieStats['failed_jobs_total'] ?? null) === null ? 'N/A' : number_format($movieStats['failed_jobs_total']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                        🗃 Database Stats
                    </div>
                </div>
                <div class="mt-4 grid gap-2">
                    <div class="flex items-center justify-between">
                        <span>Driver</span>
                        <span class="font-semibold text-amber-100">{{ $movieStats['db_driver'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Database</span>
                        <span class="font-semibold text-amber-100">{{ $movieStats['db_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Total tables</span>
                        <span class="font-semibold text-amber-100">
                            {{ ($movieStats['db_tables_total'] ?? null) === null ? 'N/A' : number_format($movieStats['db_tables_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>DB size</span>
                        <span class="font-semibold text-amber-100">
                            {{ ($movieStats['db_size_bytes'] ?? null) === null ? 'N/A' : number_format($movieStats['db_size_bytes'] / 1024 / 1024, 2).' MB' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl md:col-span-2">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                        🔎 Search (Meilisearch)
                    </div>
                </div>
                <div class="mt-4 grid gap-2">
                    <div class="flex items-center justify-between">
                        <span>Status</span>
                        <span class="font-semibold text-amber-100">
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
                        <span class="font-semibold text-amber-100">{{ $searchStats['host'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Movie index</span>
                        <span class="font-semibold text-amber-100">
                            {{ $searchStats['full_movie_index'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Records</span>
                        <span class="font-semibold text-amber-100">
                            {{ $searchStats['documents_total'] === null ? 'N/A' : number_format($searchStats['documents_total']) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Index size</span>
                        <span class="font-semibold text-amber-100">
                            {{ $searchStats['index_size_bytes'] === null ? 'N/A' : number_format($searchStats['index_size_bytes'] / 1024 / 1024, 2).' MB' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>API key</span>
                        <span class="font-semibold text-amber-100">{{ $searchStats['key_set'] ? 'Set' : 'Missing' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Queue</span>
                        <span class="font-semibold text-amber-100">
                            {{ $searchStats['queue_connection'] && $searchStats['queue_name']
                                ? $searchStats['queue_connection'] . ' / ' . $searchStats['queue_name']
                                : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>After commit</span>
                        <span class="font-semibold text-amber-100">{{ $searchStats['after_commit'] ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                        🎬 Recent Screenings
                    </div>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($recentRooms as $room)
                        <a
                            href="{{ route('rooms.stats', ['code' => $room['code']]) }}"
                            class="group block rounded-xl border border-amber-400/20 bg-slate-900/40 p-3 transition-all duration-300 hover:-translate-y-0.5 hover:border-amber-400/60 hover:bg-amber-500/10"
                        >
                            <div class="flex items-center justify-between text-xs uppercase tracking-[0.2em] text-amber-200/70">
                                <span>🎫 Screening {{ $room['code'] }}</span>
                                <span class="text-amber-100/90">{{ number_format($room['participants_count']) }} Viewers</span>
                            </div>
                            <div class="mt-2 text-sm font-semibold text-amber-100">
                                {{ $room['movie_title'] ?? 'No match yet' }}
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-300">
                                <span>👍 {{ number_format($room['likes_count']) }}</span>
                                <span>👎 {{ number_format($room['dislikes_count']) }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-amber-400/20 bg-slate-900/40 p-3 text-xs text-amber-100/80">
                            🍿 No screenings have wrapped yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="relative flex-1 overflow-hidden rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-amber-100/10" />
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 via-transparent to-purple-500/10"></div>
        </div>
    </div>
</x-layouts.app>
