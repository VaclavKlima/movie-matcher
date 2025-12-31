@php
    $participantCount = $participants->count();
@endphp

<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900" wire:poll.2s.visible="refreshState">
    {{-- Cinema Background Effects --}}
    <div class="pointer-events-none absolute inset-0">
        {{-- Film reel decorative elements --}}
        <div class="absolute -right-32 top-20 h-64 w-64 animate-film-reel rounded-full border-8 border-amber-400/20 opacity-20"></div>
        <div class="absolute -left-32 bottom-40 h-96 w-96 animate-film-reel rounded-full border-8 border-purple-400/20 opacity-10" style="animation-delay: -10s;"></div>

        {{-- Spotlight effects --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="animate-spotlight absolute inset-y-0 w-1/3 bg-gradient-to-r from-transparent via-amber-300/10 to-transparent"></div>
        </div>

        {{-- Gradient overlays --}}
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(139,92,246,0.15),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(251,191,36,0.1),transparent_50%)]"></div>

        {{-- Stars/lights --}}
        <div class="absolute inset-0">
            <div class="animate-marquee-lights absolute left-[10%] top-[15%] h-2 w-2 rounded-full bg-amber-300"></div>
            <div class="animate-marquee-lights absolute left-[85%] top-[25%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 0.5s;"></div>
            <div class="animate-marquee-lights absolute left-[60%] top-[70%] h-2 w-2 rounded-full bg-amber-300" style="animation-delay: 1s;"></div>
            <div class="animate-marquee-lights absolute left-[20%] top-[80%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 1.5s;"></div>
        </div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-6 py-10 lg:gap-10 lg:px-10 lg:py-12">
        {{-- Header with Cinema Marquee Style --}}
        <header class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-4">
                {{-- Status Badge --}}
                <div class="inline-flex items-center gap-2.5 rounded-full border-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/20 to-emerald-400/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.25em] text-emerald-300 shadow-lg shadow-emerald-500/20 backdrop-blur-sm">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                    </span>
                    Live Matching
                </div>

                {{-- Marquee Title --}}
                <div class="relative">
                    <h1 class="relative text-4xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 sm:text-5xl lg:text-6xl drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                        🎬 NOW SHOWING
                    </h1>
                    <div class="mt-1 flex gap-1">
                        <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400"></span>
                        <span class="h-1 w-12 animate-marquee-lights rounded-full bg-purple-400" style="animation-delay: 0.3s;"></span>
                        <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400" style="animation-delay: 0.6s;"></span>
                    </div>
                </div>

                <p class="text-lg text-purple-200/90 max-w-xl">
                    🍿 The lobby is sealed. Let the matching magic begin!
                </p>
            </div>

            {{-- Room Code Card --}}
            <div class="group rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/20 backdrop-blur-xl transition-all duration-300 hover:border-amber-400/50 hover:shadow-amber-500/30">
                <div class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300/80">Screening Room</div>
                <div class="mt-3 flex items-center gap-2 rounded-xl border border-amber-400/20 bg-slate-950/50 px-5 py-3">
                    <span class="text-2xl">🎫</span>
                    <span class="text-2xl font-black tracking-[0.4em] text-amber-200">{{ $roomCode }}</span>
                </div>
                @if ($isHost)
                    <x-confirm-modal
                        trigger-text="🚪 End Screening"
                        trigger-class="mt-4 w-full rounded-xl border-2 border-rose-400/50 bg-gradient-to-r from-rose-500/20 to-rose-600/20 px-4 py-2.5 text-xs font-bold uppercase tracking-[0.2em] text-rose-300 transition hover:border-rose-400 hover:from-rose-500/30 hover:to-rose-600/30 hover:shadow-lg hover:shadow-rose-500/20"
                        title="End this screening?"
                        message="This will close the room and dismiss all viewers."
                        confirm-text="Yes, end screening"
                        confirm-action="disbandRoom"
                    />
                @endif
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            {{-- Movie Card Section --}}
            <div class="group rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl transition-all duration-300 hover:border-purple-400/40 hover:shadow-purple-800/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎞️ Your Feature Film</h2>
                        <p class="mt-2 text-sm text-purple-200/80">Vote thumbs up to watch, down to skip</p>
                    </div>
                </div>

                @if ($movie)
                    {{-- Movie Card with fixed heights --}}
                    <div class="animate-card-slide mt-6 overflow-hidden rounded-2xl border-2 border-slate-700/50 bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl" x-data="{ voted: false }">
                        {{-- Poster Section - Fixed Height --}}
                        <div class="film-strip-border relative flex h-72 w-full items-center justify-center bg-gradient-to-br from-slate-950 to-slate-900 p-4 sm:h-80">
                            @if ($movie->poster_image)
                                @php
                                    $posterSrc = str_starts_with($movie->poster_image, 'data:')
                                        ? $movie->poster_image
                                        : 'data:image/jpeg;base64,'.$movie->poster_image;
                                @endphp
                                <img
                                    src="{{ $posterSrc }}"
                                    alt="{{ $movie->name }}"
                                    class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105"
                                    loading="eager"
                                />
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-50"></div>
                            @else
                                <div class="flex h-full w-full flex-col items-center justify-center gap-3 text-purple-300/60">
                                    <span class="text-5xl">🎬</span>
                                    <span class="text-sm font-semibold">No poster, pure cinema magic</span>
                                </div>
                            @endif
                        </div>

                        {{-- Movie Details - Fixed structure to prevent layout shift --}}
                        <div class="p-6 space-y-4">
                            {{-- Clapperboard Metadata --}}
                            <div class="flex items-center gap-3 rounded-lg border border-amber-400/20 bg-slate-950/50 px-4 py-2">
                                <span class="text-lg">🎬</span>
                                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-300/90">
                                    <span>{{ $movie->year }}</span>
                                    <span class="text-amber-400/50">•</span>
                                    <span>{{ $movie->duration }}</span>
                                    <span class="text-amber-400/50">•</span>
                                    <span>{{ $movie->country }}</span>
                                </div>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-2xl font-bold text-amber-50 drop-shadow-md sm:text-3xl">{{ $movie->name }}</h3>

                            {{-- Description - Fixed 4 lines --}}
                            <div class="min-h-[5.5rem]">
                                <p class="line-clamp-4 text-sm leading-relaxed text-purple-100/80">
                                    {{ $movie->description ?: 'A cinematic experience awaits. No description needed for pure entertainment.' }}
                                </p>
                            </div>

                            {{-- Genres as Cinema Tickets - Fixed min height --}}
                            @if ($movie->genres->isNotEmpty())
                                <div class="flex min-h-[2.5rem] flex-wrap gap-2">
                                    @foreach ($movie->genres as $genre)
                                        <span class="ticket-stub inline-flex items-center gap-1 rounded-r-lg border-l-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-600/10 px-3 py-1.5 text-xs font-bold text-amber-200 shadow-sm">
                                            🎫 {{ $genre->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="min-h-[2.5rem]"></div>
                            @endif

                            {{-- Actors as VIP Badges - Fixed min height --}}
                            @if ($movie->actors->isNotEmpty())
                                <div class="flex min-h-[2.5rem] flex-wrap gap-2">
                                    @foreach ($movie->actors as $actor)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-purple-400/30 bg-gradient-to-r from-purple-500/20 to-purple-600/10 px-3 py-1 text-xs font-semibold text-purple-200">
                                            ⭐ {{ $actor->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="min-h-[2.5rem]"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Vote Buttons with Animation --}}
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <button
                            type="button"
                            wire:click="vote('down')"
                            class="group/btn relative overflow-hidden rounded-2xl border-2 border-rose-400/50 bg-gradient-to-br from-rose-500/30 to-rose-600/20 px-6 py-4 font-bold text-rose-200 shadow-xl shadow-rose-900/30 transition-all duration-300 hover:scale-105 hover:border-rose-400 hover:from-rose-500/40 hover:to-rose-600/30 hover:shadow-2xl hover:shadow-rose-500/40 active:scale-95"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-2 text-base">
                                <span class="transition-transform duration-300 group-hover/btn:rotate-12 group-hover/btn:scale-125">👎</span>
                                <span>Pass</span>
                            </span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                        </button>

                        <button
                            type="button"
                            wire:click="vote('up')"
                            class="group/btn relative overflow-hidden rounded-2xl border-2 border-emerald-400/50 bg-gradient-to-br from-emerald-500/30 to-emerald-600/20 px-6 py-4 font-bold text-emerald-200 shadow-xl shadow-emerald-900/30 transition-all duration-300 hover:scale-105 hover:border-emerald-400 hover:from-emerald-500/40 hover:to-emerald-600/30 hover:shadow-2xl hover:shadow-emerald-500/40 active:scale-95"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-2 text-base">
                                <span class="transition-transform duration-300 group-hover/btn:-rotate-12 group-hover/btn:scale-125">👍</span>
                                <span>Watch It!</span>
                            </span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                        </button>
                    </div>

                    {{-- Debug Info --}}
                    @if ($debugSuggest)
                        <div class="mt-4 rounded-xl border border-dashed border-amber-400/30 bg-amber-500/10 px-4 py-3 text-xs text-amber-200 backdrop-blur-sm">
                            <div class="font-bold uppercase tracking-[0.2em] text-amber-300">Debug Info</div>
                            <div class="mt-2 flex flex-wrap gap-3 text-[11px]">
                                <span>score: {{ $debugSuggestMeta['score'] ?? 0 }}</span>
                                <span>room likes: {{ $debugSuggestMeta['room_likes'] ?? 0 }}</span>
                                <span>genre: {{ $debugSuggestMeta['genre_score'] ?? 0 }}</span>
                                <span>year: {{ $debugSuggestMeta['year_score'] ?? 0 }}</span>
                                <span>novelty: {{ $debugSuggestMeta['novelty_bonus'] ?? 0 }}</span>
                                <span>multiplier: {{ $debugSuggestMeta['genre_score_multiplier'] ?? 1 }}</span>
                                <span>total: {{ $debugSuggestMeta['total_score'] ?? 0 }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($lastChoice)
                        <div class="mt-4 flex items-center gap-3 rounded-xl border border-purple-400/30 bg-purple-500/10 px-4 py-3 text-sm text-purple-200 backdrop-blur-sm">
                            <span class="text-lg">{{ $lastChoice === 'up' ? '👍' : '👎' }}</span>
                            <span>{{ $lastChoiceMessage }}</span>
                        </div>
                    @endif
                @else
                    <div class="mt-6 flex min-h-[440px] flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-purple-400/30 bg-purple-500/5 px-6 py-12 text-center backdrop-blur-sm">
                        <span class="text-6xl opacity-50">🎞️</span>
                        <p class="text-lg font-semibold text-purple-200">No films in the vault yet</p>
                        <p class="text-sm text-purple-300/70">Add movies to your collection and start the show!</p>
                    </div>
                @endif
            </div>

            {{-- Players Section --}}
            <div class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎭 In The Theater</h2>
                        <p class="mt-2 text-sm text-purple-200/80">Everyone's seated and ready</p>
                    </div>
                    <div class="rounded-full border-2 border-purple-400/50 bg-gradient-to-br from-purple-500/20 to-purple-600/20 px-4 py-2 text-xs font-bold text-purple-200 shadow-lg shadow-purple-500/20">
                        {{ $participantCount }} 👥
                    </div>
                </div>

                <div class="mt-6 grid gap-3">
                    @foreach ($participants as $index => $participant)
                        <div class="animate-float group flex items-center justify-between rounded-xl border border-slate-600/50 bg-gradient-to-r from-slate-800/90 to-slate-700/80 px-4 py-3.5 shadow-lg transition-all duration-300 hover:border-purple-400/50 hover:shadow-purple-500/20"
                             style="animation-delay: {{ $index * 0.1 }}s;">
                            <div class="flex items-center gap-3">
                                {{-- Cinema Seat Avatar --}}
                                <span class="cinema-seat flex h-12 w-12 items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700 text-xl font-black text-white shadow-lg">
                                    {{ strtoupper(substr($participant->name ?? 'G', 0, 1)) }}
                                </span>
                                <div>
                                    <div class="font-bold text-amber-50">{{ $participant->name ?? 'Guest' }}</div>
                                    <div class="flex items-center gap-1.5 text-xs text-purple-300/80">
                                        <span>{{ $participant->is_host ? '👑 Host' : '🎬 Viewer' }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="flex items-center gap-1.5 rounded-full border border-emerald-400/50 bg-emerald-500/20 px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] text-emerald-300">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                                Ready
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Matched Movies Section --}}
        <section class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🏆 Box Office Hits</h2>
                    <p class="mt-2 text-sm text-purple-200/80">Movies that got the crowd's approval</p>
                </div>
                <div class="animate-pulse-glow rounded-full border-2 border-amber-400/50 bg-gradient-to-br from-amber-500/20 to-amber-600/20 px-4 py-2 text-xs font-bold text-amber-200">
                    {{ $matchedMovies->count() }} 🎯
                </div>
            </div>

            @if ($matchedMovies->isEmpty())
                <div class="mt-6 flex flex-col items-center gap-4 rounded-2xl border-2 border-dashed border-purple-400/30 bg-purple-500/5 px-6 py-12 text-center backdrop-blur-sm">
                    <span class="text-5xl opacity-50">🍿</span>
                    <p class="text-lg font-semibold text-purple-200">No matches yet!</p>
                    <p class="text-sm text-purple-300/70">Keep voting to find your perfect movie night pick</p>
                </div>
            @else
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($matchedMovies as $index => $match)
                        @php
                            $matchedItem = $match->movie;
                            $matchedCardSrc = $matchedItem && $matchedItem->poster_image
                                ? (str_starts_with($matchedItem->poster_image, 'data:')
                                    ? $matchedItem->poster_image
                                    : 'data:image/jpeg;base64,'.$matchedItem->poster_image)
                                : null;
                        @endphp
                        <div class="group relative overflow-hidden rounded-2xl border-2 border-slate-600/50 bg-gradient-to-br from-slate-800 to-slate-900 shadow-xl transition-all duration-300 hover:-translate-y-2 hover:border-amber-400/50 hover:shadow-2xl hover:shadow-amber-500/30"
                             style="animation: stagger-in 0.5s ease-out {{ $index * 0.1 }}s both;">
                            {{-- Marquee lights on hover --}}
                            <div class="absolute -top-1 left-0 right-0 flex justify-around opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <span class="h-2 w-2 animate-marquee-lights rounded-full bg-amber-400"></span>
                                <span class="h-2 w-2 animate-marquee-lights rounded-full bg-amber-400" style="animation-delay: 0.3s;"></span>
                                <span class="h-2 w-2 animate-marquee-lights rounded-full bg-amber-400" style="animation-delay: 0.6s;"></span>
                            </div>

                            {{-- Poster --}}
                            <div class="relative flex h-48 w-full items-center justify-center bg-gradient-to-br from-slate-950 to-slate-900 p-3">
                                @if ($matchedCardSrc)
                                    <img
                                        src="{{ $matchedCardSrc }}"
                                        alt="{{ $matchedItem->name }}"
                                        class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110"
                                    />
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60"></div>
                                @else
                                    <div class="flex flex-col items-center gap-2 text-purple-400/40">
                                        <span class="text-4xl">🎬</span>
                                        <span class="text-xs font-semibold">Cinema magic</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="p-4 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.15em] text-amber-300/80">
                                    <span>{{ $matchedItem?->year }}</span>
                                    <span class="text-amber-400/50">•</span>
                                    <span>{{ $matchedItem?->duration }}</span>
                                </div>
                                <h3 class="line-clamp-2 text-base font-bold text-amber-50">
                                    {{ $matchedItem?->name ?? 'Mystery Feature' }}
                                </h3>

                                @if ($isHost && $matchedItem)
                                    <x-confirm-modal
                                        trigger-text="🎬 Roll credits with this pick"
                                        trigger-class="mt-2 w-full rounded-xl border-2 border-rose-400/50 bg-gradient-to-r from-rose-500/20 to-rose-600/20 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em] text-rose-200 transition hover:border-rose-400 hover:from-rose-500/30 hover:to-rose-600/30 hover:shadow-lg hover:shadow-rose-500/20"
                                        title="🎬 Final feature unlocked?"
                                        message="The curtains close on this pick, and everyone heads to the stats lobby."
                                        confirm-text="Yes, roll credits"
                                        confirm-action="endRoomWithMatch({{ $matchedItem->id }})"
                                    />
                                @endif
                            </div>

                            {{-- Spotlight effect on hover --}}
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-amber-400/0 via-amber-300/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- Match Modal --}}
    <div wire:key="match-modal-{{ $showMatchModal }}-{{ $matchedMovieId }}">
        <x-match-modal :show="$showMatchModal" :matched-movie="$matchedMovie" :is-host="$isHost" />
    </div>
</div>
