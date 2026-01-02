@extends('errors.layout', ['title' => '❌ Projector Jam'])

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="animate-card-slide rounded-3xl border-2 border-rose-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-7 shadow-2xl shadow-rose-500/30 backdrop-blur-xl sm:p-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-rose-400/50 bg-rose-500/20 px-4 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-rose-100 sm:text-xs sm:tracking-[0.3em]">
                <span class="h-2 w-2 animate-pulse rounded-full bg-rose-400"></span>
                Error 500
            </div>
            <h1 class="mt-6 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)] sm:text-4xl md:text-5xl">
                ❌ Projector Jam
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-purple-200/90 sm:text-base">
                Our projector hit a snag mid-reel. Give it a moment, then roll back to the Theater Lobby or try the scene again.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-6 py-3 text-sm font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    🎭 Theater Lobby
                </a>
                <a
                    href="{{ url()->current() }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-rose-400/50 bg-rose-500/20 px-6 py-3 text-sm font-bold text-rose-100 shadow-2xl shadow-rose-500/30 transition-all duration-300 hover:scale-105 hover:border-rose-400 hover:bg-rose-500/30 hover:shadow-rose-500/50 active:scale-95"
                >
                    🎬 Try Again
                </a>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-3 text-xs text-purple-200/80">
                <span class="ticket-stub text-rose-100">Projection Paused</span>
                <span class="ticket-stub text-amber-200">Status: Fixing the Reel</span>
            </div>
        </div>

        <div class="animate-card-slide rounded-3xl border-2 border-rose-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-rose-500/20 backdrop-blur-xl" style="animation-delay: 0.1s;">
            <div class="relative overflow-hidden rounded-2xl border border-rose-400/40 bg-slate-950/60 p-6">
                <pre class="mb-5 rounded-xl border border-rose-400/30 bg-slate-950/70 p-4 text-center text-xs font-semibold text-rose-100/80">  ____  ____   ___
 |  _ \|  _ \ / _ \
 | |_) | |_) | | | |
 |  __/|  _ <| |_| |
 |_|   |_| \_\\___/
 PROJECTOR JAM
                </pre>
                <div class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-rose-900/60 to-transparent" style="animation: curtain-open 0.8s ease-out forwards; transform-origin: left;"></div>
                <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-rose-900/60 to-transparent" style="animation: curtain-open 0.8s ease-out forwards; transform-origin: right;"></div>
                <div class="relative z-10 flex flex-col items-center gap-5 text-center">
                    <div class="animate-film-reel h-24 w-24 rounded-full border-4 border-rose-300/40"></div>
                    <div class="rounded-full border border-rose-400/40 bg-rose-500/10 px-4 py-2 text-xs uppercase tracking-[0.25em] text-rose-100">
                        Emergency Reel
                    </div>
                    <p class="text-xs text-purple-200/70">
                        The show must go on. We are recalibrating the spotlight.
                    </p>
                </div>
            </div>
            <div class="mt-6 grid gap-3 text-xs text-purple-200/80">
                <div class="flex items-center gap-3 rounded-xl border border-rose-400/30 bg-rose-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-rose-400/40"></span>
                    Crew is fixing the projector.
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-amber-400/30 bg-amber-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-amber-400/40"></span>
                    Dim the lights and stand by.
                </div>
            </div>
        </div>
    </section>
@endsection
