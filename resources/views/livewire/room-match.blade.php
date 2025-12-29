@php
    $participantCount = $participants->count();
@endphp

<div class="relative overflow-hidden" wire:poll.5s="refreshState">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-16 right-8 h-72 w-72 rounded-full bg-amber-200/50 blur-3xl"></div>
        <div class="absolute bottom-[-7rem] left-[-5rem] h-80 w-80 rounded-full bg-teal-200/50 blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white/70 via-stone-50 to-amber-50/40"></div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-5xl flex-col gap-10 px-6 py-14 lg:px-10">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Matching in progress
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                    Everyone is in. Let the match begin.
                </h1>
                <p class="mt-3 text-base text-stone-600">
                    The lobby is closed. No new guests can join this room.
                </p>
            </div>
            <div class="rounded-2xl border border-stone-200/80 bg-white/90 px-5 py-4 text-sm shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Room code</div>
                <div class="mt-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-lg font-semibold tracking-[0.35em] text-stone-800">
                    {{ $roomCode }}
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-900">Your movie card</h2>
                        <p class="mt-2 text-sm text-stone-600">Thumbs up for a watch, thumbs down to pass.</p>
                    </div>
                    <div class="rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold text-stone-500">
                        Round 1
                    </div>
                </div>

                    @if ($movie)
                        <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                        <div class="relative flex h-64 w-full items-center justify-center bg-stone-900/5 p-4">
                            @if ($movie->poster_image)
                                @php
                                    $posterSrc = str_starts_with($movie->poster_image, 'data:')
                                        ? $movie->poster_image
                                        : 'data:image/jpeg;base64,'.$movie->poster_image;
                                @endphp
                                <img
                                    src="{{ $posterSrc }}"
                                    alt="{{ $movie->name }}"
                                    class="h-full w-full object-contain"
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-stone-500">
                                    No poster, just pure cinema vibes.
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">
                                <span>{{ $movie->year }}</span>
                                <span>•</span>
                                <span>{{ $movie->duration }}</span>
                                <span>•</span>
                                <span>{{ $movie->country }}</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-semibold text-stone-900">{{ $movie->name }}</h3>
                            <p class="mt-3 text-sm text-stone-600">
                                {{ $movie->description }}
                            </p>
                            @if ($movie->genres->isNotEmpty())
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($movie->genres as $genre)
                                        <span class="rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold text-stone-600">
                                            {{ $genre->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            wire:click="vote('down')"
                            class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-lg shadow-rose-200/40 transition hover:border-rose-300 hover:bg-rose-100"
                        >
                            Thumbs down
                        </button>
                        <button
                            type="button"
                            wire:click="vote('up')"
                            class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-lg shadow-emerald-200/40 transition hover:border-emerald-300 hover:bg-emerald-100"
                        >
                            Thumbs up
                        </button>
                    </div>
                    @if ($lastChoice)
                        <div class="mt-4 rounded-xl border border-dashed border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
                            {{ $lastChoice === 'up' ? 'You gave it a thumbs up. Bold choice.' : 'You passed. The director is devastated.' }}
                        </div>
                    @endif
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-5 py-8 text-center text-sm text-stone-600">
                        No movies in the vault yet. Add some and roll again.
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-900">Players in the game</h2>
                        <p class="mt-2 text-sm text-stone-600">Ready for the first match round.</p>
                    </div>
                    <div class="rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold text-stone-500">
                        {{ $participantCount }} players
                    </div>
                </div>

                <div class="mt-6 grid gap-3">
                    @foreach ($participants as $participant)
                        <div class="flex items-center justify-between rounded-xl border border-stone-200/80 bg-white px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-900 text-white">
                                    {{ strtoupper(substr($participant->name ?? 'G', 0, 1)) }}
                                </span>
                                <div>
                                    <div class="text-sm font-semibold text-stone-900">{{ $participant->name ?? 'Guest' }}</div>
                                    <div class="text-xs text-stone-500">{{ $participant->is_host ? 'Host' : 'Player' }}</div>
                                </div>
                            </div>
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-700">
                                Ready
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div
        class="fixed inset-0 z-40 flex items-center justify-center px-6"
        x-data="{ open: @entangle('showMatchModal') }"
        x-show="open"
        x-cloak
        role="dialog"
        aria-modal="true"
    >
        <div
            class="absolute inset-0 bg-stone-900/50 transition-opacity duration-300"
            x-show="open"
        ></div>
        <div
            class="relative z-10 w-full max-w-xl rounded-3xl border border-stone-200/80 bg-white/95 p-6 text-center shadow-[0_35px_80px_-50px_rgba(15,23,42,0.55)] backdrop-blur"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        >
            <div class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Match found</div>
            <h2 class="mt-3 text-3xl font-semibold text-stone-900">Everyone said yes.</h2>
            <p class="mt-2 text-sm text-stone-600">The projector hums. The snacks applaud. Roll it.</p>
            <div class="mt-5 flex items-center justify-center gap-3 text-3xl">
                <span class="animate-bounce">🎬</span>
                <span class="animate-pulse">🍿</span>
                <span class="animate-bounce">⭐</span>
            </div>

            @if ($matchedMovie)
                <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white text-left">
                    <div class="relative flex h-56 w-full items-center justify-center bg-stone-900/5 p-4">
                        @if ($matchedMovie->poster_image)
                            @php
                                $matchedPosterSrc = str_starts_with($matchedMovie->poster_image, 'data:')
                                    ? $matchedMovie->poster_image
                                    : 'data:image/jpeg;base64,'.$matchedMovie->poster_image;
                            @endphp
                            <img
                                src="{{ $matchedPosterSrc }}"
                                alt="{{ $matchedMovie->name }}"
                                class="h-full w-full object-contain"
                            />
                        @else
                            <div class="text-sm font-semibold text-stone-500">
                                No poster, still a masterpiece.
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">
                            <span>{{ $matchedMovie->year }}</span>
                            <span>•</span>
                            <span>{{ $matchedMovie->duration }}</span>
                            <span>•</span>
                            <span>{{ $matchedMovie->country }}</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-900">{{ $matchedMovie->name }}</h3>
                        <p class="mt-3 text-sm text-stone-600">
                            {{ $matchedMovie->description }}
                        </p>
                    </div>
                </div>
            @endif

            <button
                type="button"
                wire:click="continueHunting"
                class="mt-6 inline-flex items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-stone-900/20 transition hover:bg-stone-800"
            >
                Keep hunting for another movie
            </button>
        </div>
    </div>
</div>
