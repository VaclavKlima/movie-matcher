@extends('errors.layout', ['title' => '🎬 Reel Not Found'])

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="animate-card-slide rounded-3xl border-2 border-amber-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-7 shadow-2xl shadow-amber-500/30 backdrop-blur-xl sm:p-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-400/10 px-4 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-amber-200 sm:text-xs sm:tracking-[0.3em]">
                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-400"></span>
                Error 404
            </div>
            <h1 class="mt-6 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)] sm:text-4xl md:text-5xl">
                🎬 Reel Not Found
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-purple-200/90 sm:text-base">
                This scene is not on the schedule. The crowd has spoken: head back to the Theater Lobby and pick a new screening.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-6 py-3 text-sm font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    🎭 Theater Lobby
                </a>
                <a
                    href="{{ route('rooms.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-purple-400/50 bg-purple-500/20 px-6 py-3 text-sm font-bold text-purple-100 shadow-2xl shadow-purple-500/30 transition-all duration-300 hover:scale-105 hover:border-purple-400 hover:bg-purple-500/30 hover:shadow-purple-500/50 active:scale-95"
                >
                    🎫 Host Screening
                </a>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-3 text-xs text-purple-200/80">
                <span class="ticket-stub text-amber-200">Lost Scene</span>
                <span class="ticket-stub text-purple-200">Now Showing: The Show Must Go On</span>
            </div>
        </div>

        <div class="animate-card-slide rounded-3xl border-2 border-purple-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-purple-500/20 backdrop-blur-xl" style="animation-delay: 0.1s;">
            <div class="film-strip-border overflow-hidden rounded-2xl border border-slate-700/70 bg-slate-950/60 p-4">
                <pre class="rounded-xl border border-amber-400/30 bg-slate-950/70 p-4 text-center text-xs font-semibold text-amber-200/80">  ____  ___  _  _ 
 |  _ \|_ _|| || |
 | |_) || | | || |_
 |  _ < | | |__   _|
 |_| \_\___|   |_|  
 REEL NOT FOUND
                </pre>
                <div class="relative h-64 overflow-hidden rounded-xl bg-gradient-to-br from-slate-800 to-slate-900">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="relative flex items-center gap-6">
                            <div class="animate-film-reel h-20 w-20 rounded-full border-4 border-amber-300/40"></div>
                            <div class="h-10 w-20 rounded-full border-2 border-purple-300/30 bg-purple-500/10"></div>
                            <div class="animate-film-reel h-16 w-16 rounded-full border-4 border-amber-300/30" style="animation-delay: -4s;"></div>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 rounded-xl border border-purple-400/40 bg-purple-500/10 p-3 text-xs text-purple-200/80">
                        🎯 Missing frames detected. The reels are rewinding.
                    </div>
                </div>
            </div>
            <div class="mt-6 grid gap-3 text-xs text-purple-200/80">
                <div class="flex items-center gap-3 rounded-xl border border-amber-400/30 bg-amber-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-amber-400/40"></span>
                    Seats are reserved for the next reel.
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-purple-400/30 bg-purple-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-purple-400/40"></span>
                    Spotlight the lobby to restart the show.
                </div>
            </div>
        </div>
    </section>
@endsection
