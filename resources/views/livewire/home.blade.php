<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900">
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

    <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-14 lg:px-10">
        <header class="max-w-3xl">
            {{-- Brand Badge --}}
            <div class="inline-flex items-center gap-2.5 rounded-full border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-400/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.25em] text-amber-300 shadow-lg shadow-amber-500/20 backdrop-blur-sm">
                <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-amber-400"></span>
                Movie Matcher
            </div>

            {{-- Marquee Title --}}
            <div class="relative mt-6">
                <h1 class="relative text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 sm:text-6xl lg:text-7xl drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                    🎬 Match Movies<br/>In Minutes
                </h1>
                <div class="mt-2 flex gap-1">
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400"></span>
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-purple-400" style="animation-delay: 0.3s;"></span>
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400" style="animation-delay: 0.6s;"></span>
                </div>
            </div>

            <p class="mt-6 text-xl text-purple-200/90 max-w-2xl">
                🍿 Create a room, invite friends, and swipe to a shared pick without the endless group chat debate.
            </p>
        </header>

        <section class="mt-12 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            {{-- Left Column --}}
            <div class="flex flex-col gap-6">
                {{-- Create Room Card --}}
                <div class="animate-card-slide group rounded-3xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/20 backdrop-blur-xl transition-all duration-300 hover:border-amber-400/50 hover:shadow-amber-500/30">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎫 Host a Screening</h2>
                            <p class="mt-3 text-sm text-purple-200/80">
                                Start a new match session and roll out the red carpet for your crew.
                            </p>
                        </div>
                        <div class="animate-pulse-glow rounded-full border-2 border-amber-400/50 bg-gradient-to-br from-amber-500/20 to-amber-600/20 px-4 py-2 text-xs font-bold text-amber-200 shadow-lg shadow-amber-500/20">
                            Host
                        </div>
                    </div>
                    <a
                        href="{{ route('rooms.create') }}"
                        class="group/btn relative mt-6 inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-6 py-4 text-base font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                    >
                        <span class="relative z-10 flex items-center gap-2">
                            <span>🎬</span>
                            <span>Create Your Room</span>
                        </span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                    </a>
                    <div class="mt-4 flex items-center gap-2 text-xs text-emerald-300">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        </span>
                        Instant room code generation
                    </div>
                </div>

                @if ($hasPlayerCookie)
                    {{-- Finished Rooms History --}}
                    <div class="rounded-3xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/10 backdrop-blur-xl">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-amber-100 drop-shadow-lg">🎬 Recent Screenings</h3>
                                <p class="mt-2 text-sm text-purple-200/80">
                                    The crowd has spoken. Revisit the showtime picks from your last screenings.
                                </p>
                            </div>
                            <div class="rounded-full border-2 border-amber-400/40 bg-gradient-to-br from-amber-500/20 to-amber-600/20 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-amber-200 shadow-lg shadow-amber-500/20">
                                History
                            </div>
                        </div>

                        @if ($finishedRooms->isEmpty())
                            <div class="mt-4 rounded-xl border border-purple-400/30 bg-purple-500/10 p-4 text-sm text-purple-200/80">
                                🍿 No screenings in your history yet.
                            </div>
                        @else
                            <div class="mt-4 grid gap-3">
                                @foreach ($finishedRooms as $room)
                                    <a
                                        href="{{ route('rooms.stats', ['code' => $room->code]) }}"
                                        class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-700/70 bg-gradient-to-r from-slate-800/80 to-slate-900/80 p-4 transition-all duration-300 hover:border-amber-400/50 hover:shadow-lg hover:shadow-amber-500/20"
                                    >
                                        <div>
                                            <p class="text-sm font-bold text-amber-100">🎬 Screening {{ $room->code }}</p>
                                            <p class="mt-1 text-xs text-purple-200/70">
                                                Ended {{ $room->ended_at?->diffForHumans() }}
                                            </p>
                                        </div>
                                        <span class="rounded-full border border-amber-400/40 bg-amber-500/10 px-3 py-2 text-xs font-bold text-amber-200 transition-all duration-300 group-hover:bg-amber-500/20">
                                            🎯 View Stats
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- How It Works Card --}}
                <div class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/70 to-slate-900/70 p-6 backdrop-blur-xl">
                    <h3 class="text-xl font-black text-amber-100 drop-shadow-lg">🎞️ How The Magic Works</h3>
                    <div class="mt-5 grid gap-4 text-sm">
                        <div class="flex items-start gap-4 rounded-xl border border-slate-600/50 bg-gradient-to-r from-slate-800/90 to-slate-700/80 p-4 transition-all duration-300 hover:border-purple-400/50 hover:shadow-lg hover:shadow-purple-500/20">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-amber-400/50 bg-gradient-to-br from-amber-500/30 to-amber-600/20 text-base font-black text-amber-200 shadow-lg">1</span>
                            <div>
                                <p class="font-semibold text-amber-50">Pick your mood and invite friends</p>
                                <p class="mt-1 text-xs text-purple-200/70">Share the room code or link</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-xl border border-slate-600/50 bg-gradient-to-r from-slate-800/90 to-slate-700/80 p-4 transition-all duration-300 hover:border-purple-400/50 hover:shadow-lg hover:shadow-purple-500/20">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-purple-400/50 bg-gradient-to-br from-purple-500/30 to-purple-600/20 text-base font-black text-purple-200 shadow-lg">2</span>
                            <div>
                                <p class="font-semibold text-amber-50">Swipe through recommendations together</p>
                                <p class="mt-1 text-xs text-purple-200/70">Quick thumbs up or down voting</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-xl border border-slate-600/50 bg-gradient-to-r from-slate-800/90 to-slate-700/80 p-4 transition-all duration-300 hover:border-purple-400/50 hover:shadow-lg hover:shadow-purple-500/20">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-emerald-400/50 bg-gradient-to-br from-emerald-500/30 to-emerald-600/20 text-base font-black text-emerald-200 shadow-lg">3</span>
                            <div>
                                <p class="font-semibold text-amber-50">Lock in your shared movie choice</p>
                                <p class="mt-1 text-xs text-purple-200/70">Everyone agrees, showtime begins!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Join Room Card --}}
            <div class="animate-card-slide rounded-3xl border-2 border-emerald-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-emerald-500/20 backdrop-blur-xl" style="animation-delay: 0.1s;">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎭 Join a Screening</h2>
                        <p class="mt-3 text-sm text-purple-200/80">
                            Got a ticket code? Enter it here and grab your seat.
                        </p>
                    </div>
                    <div class="rounded-full border-2 border-emerald-400/50 bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 px-4 py-2 text-xs font-bold text-emerald-200 shadow-lg shadow-emerald-500/20">
                        Guest
                    </div>
                </div>

                <div class="mt-6" x-data="{ code: '' }">
                    <label class="text-sm font-bold text-amber-200" for="room-code">🎫 Room Code</label>
                    <div class="mt-3 flex flex-col gap-3">
                        <input
                            id="room-code"
                            type="text"
                            inputmode="text"
                            autocomplete="off"
                            placeholder="ABC123"
                            x-model="code"
                            x-on:input="code = code.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6)"
                            class="h-14 w-full rounded-xl border-2 border-purple-400/30 bg-slate-950/50 px-5 text-xl font-black tracking-[0.35em] text-amber-200 shadow-inner outline-none transition placeholder:text-purple-400/30 focus:border-amber-400/50 focus:bg-slate-900/70 focus:ring-2 focus:ring-amber-400/20"
                        />
                        <button
                            type="button"
                            :disabled="code.length < 4"
                            class="group/btn relative inline-flex h-14 items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/30 to-emerald-600/30 px-6 text-base font-bold text-emerald-100 shadow-2xl shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:border-emerald-400 hover:from-emerald-500/40 hover:to-emerald-600/40 hover:shadow-emerald-500/50 disabled:scale-100 disabled:cursor-not-allowed disabled:border-slate-600/50 disabled:from-slate-700/30 disabled:to-slate-800/30 disabled:text-slate-400 disabled:shadow-none active:scale-95"
                            x-on:click="if (code.length >= 4) { window.location = '{{ url('/rooms') }}/' + code + '/join' }"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                <span>🚪</span>
                                <span>Enter Theater</span>
                            </span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                        </button>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-purple-300/70">Codes are 4-6 characters</span>
                        <span class="rounded-full border border-purple-400/30 bg-slate-950/50 px-3 py-1.5 font-black tracking-[0.3em] text-amber-200" x-cloak x-text="code.length ? code : '------'"></span>
                    </div>
                </div>

                <div class="mt-8 flex items-start gap-3 rounded-xl border border-dashed border-purple-400/30 bg-purple-500/10 p-4 text-sm backdrop-blur-sm">
                    <span class="text-xl">💡</span>
                    <div>
                        <p class="font-semibold text-purple-200">Pro Tip</p>
                        <p class="mt-1 text-xs text-purple-300/80">Keep the room open while friends join so no one misses the opening act.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
