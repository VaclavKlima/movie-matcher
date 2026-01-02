@extends('errors.layout', ['title' => '⚠️ Ticket Expired'])

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="animate-card-slide rounded-3xl border-2 border-amber-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-7 shadow-2xl shadow-amber-500/30 backdrop-blur-xl sm:p-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/50 bg-gradient-to-r from-amber-500/20 to-amber-400/10 px-4 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-amber-200 sm:text-xs sm:tracking-[0.3em]">
                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-400"></span>
                Error 419
            </div>
            <h1 class="mt-6 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)] sm:text-4xl md:text-5xl">
                ⚠️ Ticket Expired
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-purple-200/90 sm:text-base">
                Your ticket timed out in the lobby. Reload the page to grab a fresh ticket and continue the screening.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ url()->current() }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-6 py-3 text-sm font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    🎬 Reload Ticket
                </a>
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-purple-400/50 bg-purple-500/20 px-6 py-3 text-sm font-bold text-purple-100 shadow-2xl shadow-purple-500/30 transition-all duration-300 hover:scale-105 hover:border-purple-400 hover:bg-purple-500/30 hover:shadow-purple-500/50 active:scale-95"
                >
                    🎭 Theater Lobby
                </a>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-3 text-xs text-purple-200/80">
                <span class="ticket-stub text-amber-200">Session Over</span>
                <span class="ticket-stub text-purple-200">Grab Another Ticket</span>
            </div>
        </div>

        <div class="animate-card-slide rounded-3xl border-2 border-purple-400/40 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-purple-500/20 backdrop-blur-xl" style="animation-delay: 0.1s;">
            <div class="rounded-2xl border border-purple-400/40 bg-slate-950/60 p-6">
                <pre class="mb-5 rounded-xl border border-amber-400/30 bg-slate-950/70 p-4 text-center text-xs font-semibold text-amber-200/80">  _  _   _  ___  
 | || | / |/ _ \ 
 | || |_| | (_) |
 |__   _| |\__, |
    |_| |_|  /_/ 
 TICKET EXPIRED
                </pre>
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.25em] text-purple-200/70">🎫 Ticket Booth</p>
                        <p class="text-lg font-bold text-amber-100">Fresh Passes Available</p>
                    </div>
                    <div class="animate-pulse-glow rounded-full border-2 border-amber-400/50 bg-gradient-to-br from-amber-500/20 to-amber-600/20 px-3 py-2 text-xs font-bold text-amber-200">
                        Booth Open
                    </div>
                </div>
                <div class="mt-5 flex h-28 items-center justify-center rounded-xl border border-slate-700/70 bg-gradient-to-br from-slate-800 to-slate-900">
                    <div class="flex items-center gap-6">
                        <div class="h-14 w-24 rounded-lg border border-amber-400/30 bg-amber-500/10"></div>
                        <div class="animate-film-reel h-14 w-14 rounded-full border-4 border-purple-300/30"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 grid gap-3 text-xs text-purple-200/80">
                <div class="flex items-center gap-3 rounded-xl border border-amber-400/30 bg-amber-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-amber-400/40"></span>
                    Doors open when your ticket is stamped.
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-purple-400/30 bg-purple-500/10 p-3">
                    <span class="cinema-seat h-6 w-6 bg-purple-400/40"></span>
                    The show resumes right after reload.
                </div>
            </div>
        </div>
    </section>
@endsection
