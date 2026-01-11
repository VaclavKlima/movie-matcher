@php
    $participantCount = $stats->participants->count();
    $matchCount = $stats->matchedMovies->count();
    $hostParticipant = $stats->participants->firstWhere('is_host', true);
    $avatars = config('room.avatars', []);
    if (! is_array($avatars) || $avatars === []) {
        $avatars = [
            ['id' => 'popcorn', 'label' => 'Popcorn', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'ring' => 'ring-amber-400/40'],
        ];
    }
    $avatarMap = [];
    foreach ($avatars as $avatarOption) {
        $avatarMap[$avatarOption['id']] = $avatarOption;
    }
@endphp

<div class="relative min-h-screen">

    <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-10 px-6 py-10 lg:gap-12 lg:px-10 lg:py-12">
        {{-- Header --}}
        <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2.5 rounded-full border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-600/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.25em] text-amber-200 shadow-lg shadow-amber-500/20 backdrop-blur-sm">
                    <span class="text-base">🎬</span>
                    Screening Stats
                </div>
                <h1 class="text-4xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 sm:text-5xl drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                    🎉 The Credits Roll
                </h1>
                <p class="max-w-xl text-sm sm:text-base text-purple-200/90 leading-relaxed">
                    A cinematic breakdown of the room, the crowd, and the winning reel.
                </p>
            </div>

            <div class="flex flex-col gap-4">
                <div class="animate-pulse-glow rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-5 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
                    <div class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300/80">Room Code</div>
                    <div class="mt-3 flex items-center gap-2 rounded-xl border border-amber-400/20 bg-slate-950/50 px-5 py-3">
                        <span class="text-2xl">🎫</span>
                        <span class="text-2xl font-black tracking-[0.35em] text-amber-200">{{ $stats->room->code }}</span>
                    </div>
                </div>

                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-5 py-3 text-xs sm:text-sm font-bold uppercase tracking-[0.2em] text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    <span>🎬</span>
                    <span>Back to Main</span>
                </a>
            </div>
        </header>

        {{-- Selected Movie --}}
        <section class="rounded-3xl border-2 border-amber-400/40 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-6 shadow-2xl shadow-amber-500/30 backdrop-blur-xl transition-all duration-300 hover:border-amber-400/60 hover:shadow-amber-500/40">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎯 Final Feature</h2>
                    <p class="mt-2 text-sm text-purple-200/80">The pick that closed the curtains</p>
                </div>
                @if ($stats->selectedMovie && $stats->finalMatchNumber)
                    <div class="animate-float ticket-stub inline-flex items-center gap-2 rounded-r-lg border-l-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-600/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-200">
                        🎟️ Match #{{ $stats->finalMatchNumber }}
                    </div>
                @endif
            </div>

            @if ($stats->selectedMovie)
                <div class="mt-6 grid gap-6 lg:grid-cols-[0.4fr_0.6fr]">
                    <div class="film-strip-border relative flex h-72 w-full items-center justify-center overflow-hidden rounded-2xl border-2 border-slate-700/50 bg-gradient-to-br from-slate-950 to-slate-900 p-4 shadow-xl transition-transform duration-300 hover:scale-[1.01]">
                        @if ($stats->selectedMovie->poster_url)
                            <img
                                src="{{ $stats->selectedMovie->poster_url }}"
                                alt="{{ $stats->selectedMovie->name }}"
                                class="h-full w-full object-contain"
                            />
                        @else
                            <div class="flex flex-col items-center gap-3 text-purple-300/60">
                                <span class="text-6xl">🎬</span>
                                <span class="text-sm font-semibold">No poster, still a blockbuster</span>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 rounded-lg border border-amber-400/20 bg-slate-950/50 px-4 py-2">
                            <span class="text-lg">🎬</span>
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-300/90">
                                <span>{{ $stats->selectedMovie->year }}</span>
                                <span class="text-amber-400/50">•</span>
                                <span>{{ $stats->selectedMovie->duration }}</span>
                                <span class="text-amber-400/50">•</span>
                                <span>{{ $stats->selectedMovie->country }}</span>
                            </div>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-bold text-amber-50">{{ $stats->selectedMovie->name }}</h3>
                        <p class="text-sm sm:text-base leading-relaxed text-purple-100/80">
                            {{ $stats->selectedMovie->description ?: 'The final reel is a mystery, but the crowd loved it.' }}
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl border border-amber-400/50 bg-amber-500/10 px-4 py-3 text-center transition-all duration-300 hover:border-amber-300/70 hover:bg-amber-500/20">
                                <div class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">Final Match #</div>
                                <div class="mt-2 text-2xl font-black text-amber-100">{{ $stats->finalMatchNumber ?? 'TBD' }}</div>
                            </div>
                            <div class="rounded-xl border border-emerald-400/50 bg-emerald-500/10 px-4 py-3 text-center transition-all duration-300 hover:border-emerald-300/70 hover:bg-emerald-500/20">
                                <div class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">Runtime</div>
                                <div class="mt-2 text-2xl font-black text-emerald-100">{{ $stats->roomDurationLabel ?? 'TBD' }}</div>
                            </div>
                            <div class="rounded-xl border border-amber-400/50 bg-amber-500/10 px-4 py-3 text-center transition-all duration-300 hover:border-amber-300/70 hover:bg-amber-500/20">
                                <div class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">Audience Match</div>
                                <div class="mt-2 text-2xl font-black text-amber-100">
                                    {{ $stats->selectedAudienceYes }} / {{ $stats->nonHostCount }}
                                </div>
                                <div class="mt-1 text-xs font-semibold text-amber-200/80">{{ $stats->selectedAudienceYesPercent }}% of audience</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 flex min-h-[260px] flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-amber-400/40 bg-amber-500/5 px-6 py-10 text-center backdrop-blur-sm">
                    <span class="text-5xl opacity-70">🎭</span>
                    <p class="text-lg font-semibold text-amber-100">No final feature yet</p>
                    <p class="text-sm text-purple-200/80">Once a movie is locked in, it will shine here.</p>
                </div>
            @endif
        </section>

        {{-- Cast Spotlight --}}
        <section class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl transition-all duration-300 hover:border-purple-400/50 hover:shadow-purple-500/30">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎭 Ensemble Spotlight</h2>
                    <p class="mt-2 text-sm text-purple-200/80">The troupe behind tonight's finale</p>
                </div>
                <div class="rounded-full border-2 border-purple-400/50 bg-gradient-to-br from-purple-500/20 to-purple-600/20 px-4 py-2 text-xs font-bold text-purple-200 shadow-lg shadow-purple-500/20">
                    {{ $participantCount }} 👥
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-amber-400/40 bg-amber-500/10 p-4 transition-all duration-300 hover:border-amber-300/70 hover:bg-amber-500/20">
                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">Total Votes</div>
                    <div class="mt-2 text-2xl font-black text-amber-100">{{ $stats->totalVotes }}</div>
                </div>
                <div class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 p-4 transition-all duration-300 hover:border-emerald-300/70 hover:bg-emerald-500/20">
                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">Yes Votes</div>
                    <div class="mt-2 text-2xl font-black text-emerald-100">{{ $stats->totalYesVotes }}</div>
                </div>
                <div class="rounded-2xl border border-purple-400/40 bg-purple-500/10 p-4 transition-all duration-300 hover:border-purple-300/70 hover:bg-purple-500/20">
                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-purple-200">Approval Rate</div>
                    <div class="mt-2 text-2xl font-black text-purple-100">{{ $stats->overallApproval }}%</div>
                </div>
                <div class="rounded-2xl border border-amber-400/40 bg-amber-500/10 p-4 transition-all duration-300 hover:border-amber-300/70 hover:bg-amber-500/20">
                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">Matches</div>
                    <div class="mt-2 text-2xl font-black text-amber-100">{{ $matchCount }}</div>
                </div>
            </div>

            <div class="mt-6 grid gap-4">
                @if ($stats->currentParticipant && $stats->currentParticipantStats)
                    <div class="rounded-2xl border-2 border-amber-400/50 bg-gradient-to-br from-slate-800/90 to-slate-900/80 p-4 shadow-lg shadow-amber-500/20 transition-all duration-300 hover:border-amber-300/70 hover:shadow-amber-500/30">
                        @php
                            $currentAvatarData = $stats->currentParticipant
                                ? ($avatarMap[$stats->currentParticipant->avatar] ?? $avatars[0])
                                : $avatars[0];
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="cinema-seat flex h-12 w-12 shrink-0 items-center justify-center text-xl font-black shadow-lg {{ $currentAvatarData['bg'] }} {{ $currentAvatarData['text'] }} {{ $currentAvatarData['ring'] }} ring-2 ring-inset ring-offset-2 ring-offset-slate-900">
                                <x-movie-avatar-icon :id="$currentAvatarData['id']" class="h-6 w-6" />
                            </span>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Your Seat</div>
                                <div class="text-lg font-semibold text-amber-50">{{ $stats->currentParticipant->name ?? 'You' }}</div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-2 py-2">
                                <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-emerald-200">Yes</div>
                                <div class="mt-1 text-lg font-black text-emerald-100">{{ $stats->currentParticipantStats->yes }}</div>
                            </div>
                            <div class="rounded-xl border border-rose-400/40 bg-rose-500/10 px-2 py-2">
                                <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-rose-200">No</div>
                                <div class="mt-1 text-lg font-black text-rose-100">{{ $stats->currentParticipantStats->no }}</div>
                            </div>
                            <div class="rounded-xl border border-amber-400/40 bg-amber-500/10 px-2 py-2">
                                <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-amber-200">Yes %</div>
                                <div class="mt-1 text-lg font-black text-amber-100">{{ $stats->currentParticipantStats->approval }}%</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($stats->nonHostStats->toCollection()->isEmpty())
                <div class="mt-6 flex min-h-[220px] flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-slate-600/50 bg-slate-900/40 px-6 py-10 text-center text-purple-200/80">
                    <span class="text-4xl opacity-70">🎬</span>
                    <p class="text-sm">No additional cast stats to show.</p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stats->nonHostStats as $stat)
                        @php
                            $avatarData = $avatarMap[$stat->participant->avatar] ?? $avatars[0];
                        @endphp
                        <div class="rounded-2xl border border-purple-400/30 bg-gradient-to-r from-slate-800/90 to-slate-700/80 p-4 shadow-lg transition-all duration-300 hover:border-purple-400/50 hover:shadow-purple-500/20">
                            <div class="flex items-center gap-3">
                                <span class="cinema-seat flex h-12 w-12 shrink-0 items-center justify-center text-xl font-black shadow-lg {{ $avatarData['bg'] }} {{ $avatarData['text'] }} {{ $avatarData['ring'] }} ring-2 ring-inset ring-offset-2 ring-offset-slate-900">
                                    <x-movie-avatar-icon :id="$avatarData['id']" class="h-6 w-6" />
                                </span>
                                <div>
                                    <div class="font-bold text-amber-50">{{ $stat->participant->name ?? 'Guest' }}</div>
                                    <div class="text-xs text-purple-300/80">
                                        {{ $stat->participant->is_host ? 'Director' : 'Viewer' }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-2 py-2">
                                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-emerald-200">Yes</div>
                                    <div class="mt-1 text-lg font-black text-emerald-100">{{ $stat->yes }}</div>
                                </div>
                                <div class="rounded-xl border border-rose-400/40 bg-rose-500/10 px-2 py-2">
                                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-rose-200">No</div>
                                    <div class="mt-1 text-lg font-black text-rose-100">{{ $stat->no }}</div>
                                </div>
                                <div class="rounded-xl border border-amber-400/40 bg-amber-500/10 px-2 py-2">
                                    <div class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-amber-200">Yes %</div>
                                    <div class="mt-1 text-lg font-black text-amber-100">{{ $stat->approval }}%</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Audience Genre Picks --}}
        <section class="rounded-3xl border-2 border-amber-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/30 backdrop-blur-xl transition-all duration-300 hover:border-amber-300/60 hover:shadow-amber-500/40">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎟️ Audience Genre Picks</h2>
                    <p class="mt-2 text-sm text-purple-200/80">Who cheered and who passed on each genre.</p>
                </div>
                <div class="ticket-stub inline-flex items-center gap-2 rounded-r-lg border-l-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-600/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-200">
                    🎬 Final Taste
                </div>
            </div>

            @if ($stats->participantGenrePreferences->isEmpty())
                <div class="mt-6 flex min-h-[200px] flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-amber-400/30 bg-slate-900/40 px-6 py-10 text-center text-purple-200/80">
                    <span class="text-4xl opacity-70">🍿</span>
                    <p class="text-sm">No genre picks were logged.</p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stats->participantGenrePreferences as $entry)
                        @php
                            $participant = $entry['participant'];
                            $avatarData = $avatarMap[$participant->avatar] ?? $avatars[0];
                            $preferred = $entry['preferred'];
                            $avoided = $entry['avoided'];
                        @endphp
                        <div class="rounded-2xl border border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/80 p-4 shadow-lg transition-all duration-300 hover:border-amber-300/60 hover:shadow-amber-500/20">
                            <div class="flex items-center gap-3">
                                <span class="cinema-seat flex h-12 w-12 shrink-0 items-center justify-center text-xl font-black shadow-lg {{ $avatarData['bg'] }} {{ $avatarData['text'] }} {{ $avatarData['ring'] }} ring-2 ring-inset ring-offset-2 ring-offset-slate-900">
                                    <x-movie-avatar-icon :id="$avatarData['id']" class="h-6 w-6" />
                                </span>
                                <div>
                                    <div class="font-bold text-amber-50">{{ $participant->name ?? 'Guest' }}</div>
                                    <div class="text-xs text-purple-300/80">
                                        {{ $participant->is_host ? '👑 Host' : '🎬 Viewer' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">👍 Watch It!</div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @forelse ($preferred as $genre)
                                            <span class="ticket-stub inline-flex items-center rounded-r-lg border-l-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/20 to-emerald-600/10 px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-emerald-200">
                                                {{ $genre }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-purple-300/80">No cheers logged.</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase tracking-[0.2em] text-rose-200">👎 Pass</div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @forelse ($avoided as $genre)
                                            <span class="ticket-stub inline-flex items-center rounded-r-lg border-l-2 border-rose-400/50 bg-gradient-to-r from-rose-500/20 to-rose-600/10 px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-rose-200">
                                                {{ $genre }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-purple-300/80">No passes logged.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Match Reel --}}
        <section class="rounded-3xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/20 backdrop-blur-xl transition-all duration-300 hover:border-amber-400/50 hover:shadow-amber-500/30">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎬 Match Reel</h2>
                    <p class="mt-2 text-sm text-purple-200/80">The hits, the near-misses, and the cold cuts</p>
                </div>
                <div class="animate-float rounded-full border-2 border-amber-400/40 bg-amber-500/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-200">
                    Spotlight
                </div>
            </div>

            <div class="mt-6 space-y-6">
                <div class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 p-4 transition-all duration-300 hover:border-emerald-300/70 hover:bg-emerald-500/20">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-emerald-100">⭐ Other Matched Movies</h3>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">{{ $stats->otherMatchedMovies->count() }}</span>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @forelse ($stats->otherMatchedMovies as $match)
                            @php
                                $movie = $match->movie;
                                $posterSrc = $movie?->poster_url;
                            @endphp
                            <div class="rounded-xl border border-emerald-400/30 bg-slate-900/60 p-3 text-center transition-all duration-300 hover:border-emerald-300/60 hover:bg-emerald-500/10">
                                <div class="film-strip-border relative flex h-32 w-full items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-slate-950 to-slate-900">
                                    @if ($posterSrc)
                                        <img src="{{ $posterSrc }}" alt="{{ $movie->name }}" class="h-full w-full object-contain" loading="lazy" />
                                    @else
                                        <span class="text-2xl text-emerald-200/70">🎬</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs font-semibold text-emerald-100 line-clamp-2">{{ $movie?->name ?? 'Mystery Match' }}</div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-emerald-400/30 bg-slate-900/40 px-4 py-6 text-center text-xs text-emerald-200/80">
                                No extra matches yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-400/40 bg-amber-500/10 p-4 transition-all duration-300 hover:border-amber-300/70 hover:bg-amber-500/20">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-amber-100">🎯 Almost Matched</h3>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-amber-200">{{ count($stats->almostMatchedIds) }}</span>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @forelse ($stats->almostMatchedIds as $movieId)
                            @php
                                $movie = $stats->almostMatchedMovies->get($movieId);
                                $voteStats = $stats->voteStatsByMovieId->get($movieId);
                                $posterSrc = $movie?->poster_url;
                            @endphp
                            <div class="rounded-xl border border-amber-400/30 bg-slate-900/60 p-3 text-center transition-all duration-300 hover:border-amber-300/60 hover:bg-amber-500/10">
                                <div class="film-strip-border relative flex h-32 w-full items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-slate-950 to-slate-900">
                                    @if ($posterSrc)
                                        <img src="{{ $posterSrc }}" alt="{{ $movie?->name ?? 'Almost match' }}" class="h-full w-full object-contain" loading="lazy" />
                                    @else
                                        <span class="text-2xl text-amber-200/70">🎬</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs font-semibold text-amber-100 line-clamp-2">{{ $movie?->name ?? 'Near Miss' }}</div>
                                <div class="mt-1 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-amber-200/80">
                                    {{ $voteStats?->yes ?? 0 }} yes / {{ $voteStats?->total ?? 0 }} votes
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-amber-400/30 bg-slate-900/40 px-4 py-6 text-center text-xs text-amber-200/80">
                                No close calls yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-rose-400/40 bg-rose-500/10 p-4 transition-all duration-300 hover:border-rose-300/70 hover:bg-rose-500/20">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-rose-100">💥 Most Disliked</h3>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-rose-200">{{ count($stats->mostDislikedIds) }}</span>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @forelse ($stats->mostDislikedIds as $movieId)
                            @php
                                $movie = $stats->mostDislikedMovies->get($movieId);
                                $voteStats = $stats->voteStatsByMovieId->get($movieId);
                                $posterSrc = $movie?->poster_url;
                            @endphp
                            <div class="rounded-xl border border-rose-400/30 bg-slate-900/60 p-3 text-center transition-all duration-300 hover:border-rose-300/60 hover:bg-rose-500/10">
                                <div class="film-strip-border relative flex h-32 w-full items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-slate-950 to-slate-900">
                                    @if ($posterSrc)
                                        <img src="{{ $posterSrc }}" alt="{{ $movie?->name ?? 'Most disliked' }}" class="h-full w-full object-contain" loading="lazy" />
                                    @else
                                        <span class="text-2xl text-rose-200/70">🎬</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs font-semibold text-rose-100 line-clamp-2">{{ $movie?->name ?? 'Cold Cut' }}</div>
                                <div class="mt-1 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-rose-200/80">
                                    {{ $voteStats?->no ?? 0 }} boos / {{ $voteStats?->total ?? 0 }} votes
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-rose-400/30 bg-slate-900/40 px-4 py-6 text-center text-xs text-rose-200/80">
                                No flops yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Movie, Genre, Actor Stats --}}
        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border-2 border-amber-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/30 backdrop-blur-xl transition-all duration-300 hover:border-amber-300/60 hover:shadow-amber-500/40">
                <h3 class="text-xl font-black text-amber-100 drop-shadow-lg">🎬 Movie Stats</h3>
                <p class="mt-2 text-sm text-purple-200/80">Highlights from the vault</p>
                <div class="mt-6 space-y-3 text-sm text-purple-100/80">
                    <div class="flex items-center justify-between rounded-xl border border-slate-600/50 bg-slate-900/50 px-4 py-2">
                        <span>Total Matches</span>
                        <span class="font-bold text-amber-100">{{ $matchCount }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-600/50 bg-slate-900/50 px-4 py-2">
                        <span>Final Feature</span>
                        <span class="font-bold text-amber-100">{{ $stats->selectedMovie?->name ?? 'TBD' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-600/50 bg-slate-900/50 px-4 py-2">
                        <span>Total Votes</span>
                        <span class="font-bold text-amber-100">{{ $stats->totalVotes }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border-2 border-purple-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl transition-all duration-300 hover:border-purple-300/60 hover:shadow-purple-500/30">
                <h3 class="text-xl font-black text-amber-100 drop-shadow-lg">🎟️ Top Genres</h3>
                <p class="mt-2 text-sm text-purple-200/80">The crowd’s favorite tickets</p>
                <div class="mt-6 flex flex-wrap gap-2">
                    @forelse ($stats->genreCounts as $genre => $count)
                        <span class="ticket-stub inline-flex items-center gap-2 rounded-r-lg border-l-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-600/10 px-3 py-1.5 text-xs font-bold text-amber-200">
                            🎟️ {{ $genre }} <span class="text-amber-100/80">({{ $count }})</span>
                        </span>
                    @empty
                        <span class="text-sm text-purple-200/80">No genre stats yet.</span>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border-2 border-emerald-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-emerald-500/20 backdrop-blur-xl transition-all duration-300 hover:border-emerald-300/60 hover:shadow-emerald-500/30">
                <h3 class="text-xl font-black text-amber-100 drop-shadow-lg">⭐ Top Actors</h3>
                <p class="mt-2 text-sm text-purple-200/80">Most featured performers</p>
                <div class="mt-6 flex flex-wrap gap-2">
                    @forelse ($stats->actorCounts as $actor => $count)
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-200">
                            ⭐ {{ $actor }} <span class="text-emerald-100/80">({{ $count }})</span>
                        </span>
                    @empty
                        <span class="text-sm text-purple-200/80">No actor stats yet.</span>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>

