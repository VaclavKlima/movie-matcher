<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎬 Administration
            </div>
            <h1 class="mt-2 text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)] sm:text-3xl">
                📈 Trends Dashboard
            </h1>
            <p class="mt-2 max-w-2xl text-sm text-purple-200/90">
                The crowd has spoken. Track what is winning hearts across screenings.
            </p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎫 Screening Totals
            </div>
            <div class="mt-4 grid gap-2">
                <div class="flex items-center justify-between">
                    <span>Total screenings</span>
                    <span class="font-semibold text-amber-100">{{ number_format($stats['total_rooms']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Screenings ended</span>
                    <span class="font-semibold text-amber-100">{{ number_format($stats['ended_rooms']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Now showing</span>
                    <span class="font-semibold text-emerald-200">{{ number_format($stats['active_rooms']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Theater lobby</span>
                    <span class="font-semibold text-purple-200">{{ number_format($stats['lobby_rooms']) }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎯 Match Momentum
            </div>
            <div class="mt-4 grid gap-2">
                <div class="flex items-center justify-between">
                    <span>Total matches</span>
                    <span class="font-semibold text-amber-100">{{ number_format($stats['matched_rooms']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Matches this week</span>
                    <span class="font-semibold text-amber-100">{{ number_format($stats['last_week_rooms']) }}</span>
                </div>
            </div>
            <div class="mt-4 rounded-xl border border-amber-400/20 bg-slate-900/40 p-3">
                <div class="text-xs uppercase tracking-[0.2em] text-amber-200/70">
                    📅 Last 7 days
                </div>
                <div class="mt-3 space-y-2 text-xs text-slate-300">
                    @forelse ($stats['matches_by_day'] as $day)
                        <div class="flex items-center justify-between">
                            <span>{{ $day['day'] }}</span>
                            <span class="font-semibold text-amber-100">{{ number_format($day['total']) }}</span>
                        </div>
                    @empty
                        <div class="text-amber-100/80">No matches yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                ⭐ Top Winners
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($stats['top_movies'] as $movie)
                    <div class="flex items-center justify-between rounded-xl border border-amber-400/20 bg-slate-900/40 px-3 py-2">
                        <span class="text-sm font-semibold text-amber-100">{{ $movie->name }}</span>
                        <span class="text-xs uppercase tracking-[0.2em] text-amber-200/80">{{ number_format($movie->total) }} wins</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-amber-400/20 bg-slate-900/40 p-3 text-xs text-amber-100/80">
                        🍿 No winning picks yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎞️ Top Genres
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($stats['top_genres'] as $genre)
                    <div class="flex items-center justify-between rounded-xl border border-amber-400/20 bg-slate-900/40 px-3 py-2">
                        <span class="font-semibold text-amber-100">{{ $genre->name }}</span>
                        <span class="text-xs uppercase tracking-[0.2em] text-amber-200/80">{{ number_format($genre->total) }}</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-amber-400/20 bg-slate-900/40 p-3 text-xs text-amber-100/80">
                        🎬 No genre trends yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-5 text-sm text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                🎭 Top Actors
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($stats['top_actors'] as $actor)
                    <div class="flex items-center justify-between rounded-xl border border-amber-400/20 bg-slate-900/40 px-3 py-2">
                        <span class="font-semibold text-amber-100">{{ $actor->name }}</span>
                        <span class="text-xs uppercase tracking-[0.2em] text-amber-200/80">{{ number_format($actor->total) }}</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-amber-400/20 bg-slate-900/40 p-3 text-xs text-amber-100/80">
                        🎬 No actor trends yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
