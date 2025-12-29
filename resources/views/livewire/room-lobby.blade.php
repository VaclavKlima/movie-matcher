@php
    $avatarMap = [];
    foreach ($avatars as $avatarOption) {
        $avatarMap[$avatarOption['id']] = $avatarOption;
    }
    $selectedAvatar = $avatarMap[$avatar] ?? $avatars[0];
    $participantCount = $participants->count();
    $hasEnoughPlayers = $participantCount >= 2;
    $everyoneReady = $participants->every(fn ($participant) => (bool) $participant->is_ready);
    $canStartMatching = $hasEnoughPlayers && $everyoneReady;
    $readyBadgeClasses = $isReady
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-stone-200 bg-white text-stone-500';
@endphp

<div class="relative overflow-hidden" wire:poll.5s="refreshParticipants" x-data="kickModal(@this)">
    @if ($isKicked)
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-16 right-8 h-72 w-72 rounded-full bg-rose-200/50 blur-3xl"></div>
            <div class="absolute bottom-[-7rem] left-[-5rem] h-80 w-80 rounded-full bg-amber-200/60 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/80 via-rose-50 to-amber-50/60"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-3xl flex-col items-center justify-center gap-6 px-6 py-16 text-center">
            <div class="rounded-3xl border border-stone-200/80 bg-white/90 px-8 py-10 shadow-[0_30px_80px_-50px_rgba(15,23,42,0.45)] backdrop-blur">
                <div class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Ejected</div>
                <h1 class="mt-4 text-3xl font-semibold text-stone-900 sm:text-4xl">
                    You got kicked out of the screening.
                </h1>
                <p class="mt-3 text-base text-stone-600">
                    The bouncer says: "No ticket, no popcorn."
                </p>
                <pre class="mt-6 text-left text-sm text-stone-500">
      .-"""-.
     / -   -  \
    |  .-. .- |
    |  \o| |o (
    \     ^    \
     '.  )--'  /
       '-...-'   POOF!
                </pre>
                <a
                    href="{{ route('home') }}"
                    class="mt-6 inline-flex items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-stone-900/20 transition hover:bg-stone-800"
                >
                    Back to home
                </a>
            </div>
        </div>
    @else
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-16 right-8 h-72 w-72 rounded-full bg-amber-200/50 blur-3xl"></div>
            <div class="absolute bottom-[-7rem] left-[-5rem] h-80 w-80 rounded-full bg-teal-200/50 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/70 via-stone-50 to-amber-50/40"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-10 px-6 py-14 lg:px-10">
            <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500 shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Room open
                    </div>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                        Waiting for your crew to join.
                    </h1>
                    <p class="mt-3 text-base text-stone-600">
                        Share the link or room code so everyone can jump in and start matching.
                    </p>
                </div>
                <div class="rounded-2xl border border-stone-200/80 bg-white/90 px-5 py-4 text-sm shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Room code</div>
                    <div
                        id="room-code-panel"
                        class="mt-2 flex items-center gap-3"
                        x-data="roomCodePanel('Room code copied')"
                        x-on:show-room-code.window="showCode = true"
                    >
                        <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-2 text-lg font-semibold tracking-[0.35em] text-stone-800">
                            <span x-text="showCode ? '{{ $roomCode }}' : '******'"></span>
                        </div>
                        <button
                            type="button"
                            class="rounded-full border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 transition hover:border-stone-300"
                            x-on:click="showCode = !showCode"
                        >
                            <span x-text="showCode ? 'Hide code' : 'Show code'"></span>
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 transition hover:border-stone-300"
                        x-on:click="copy('{{ $roomCode }}')"
                        >
                            Copy code
                        </button>
                    </div>
                </div>
            </header>

            <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="flex flex-col gap-6">
                    <div class="rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold text-stone-900">Share this room</h2>
                                <p class="mt-2 text-sm text-stone-600">Send the invite link to friends and keep the room open.</p>
                            </div>
                            <div class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Host
                            </div>
                        </div>
                        <div class="mt-5 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700">
                            {{ $shareUrl }}
                        </div>
                        <div
                            class="mt-4 flex flex-wrap items-center gap-3"
                            x-data="clipboardHelper('Invite link copied')"
                        >
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-stone-900/15 transition hover:bg-stone-800"
                                x-on:click="copy('{{ $shareUrl }}')"
                            >
                                Share link
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-300"
                                x-on:click="$dispatch('show-room-code'); document.getElementById('room-code-panel')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                            >
                                Show code
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-300"
                                x-on:click="copy('{{ $roomCode }}')"
                            >
                                Copy code
                            </button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-stone-200/80 bg-white/80 p-6 backdrop-blur">
                        <h2 class="text-xl font-semibold text-stone-900">Your profile</h2>
                        <p class="mt-2 text-sm text-stone-600">Set your name and pick a movie-themed avatar.</p>

                        <div class="mt-5">
                            <label class="text-sm font-semibold text-stone-700" for="display-name">Display name</label>
                            <input
                                id="display-name"
                                type="text"
                                wire:model.live.debounce.500ms="name"
                                placeholder="Type your name"
                                class="mt-2 h-11 w-full rounded-xl border border-stone-200 bg-stone-50 px-4 text-sm text-stone-800 shadow-inner outline-none transition focus:border-stone-400 focus:bg-white"
                            />
                        </div>

                        <div class="mt-6">
                            <div class="text-sm font-semibold text-stone-700">Choose your avatar</div>
                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($avatars as $avatarOption)
                                    @php
                                        $isSelected = $avatar === $avatarOption['id'];
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="$set('avatar', '{{ $avatarOption['id'] }}')"
                                        class="flex items-center gap-3 rounded-xl border border-stone-200 bg-white px-3 py-3 text-sm font-semibold text-stone-700 shadow-sm transition hover:border-stone-300 {{ $isSelected ? 'ring-2 '.$avatarOption['ring'].' border-stone-300' : '' }}"
                                    >
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full {{ $avatarOption['bg'] }} {{ $avatarOption['text'] }}">
                                            <x-movie-avatar-icon :id="$avatarOption['id']" class="h-5 w-5" />
                                        </span>
                                        <span>{{ $avatarOption['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-stone-900">Guests in the room</h2>
                            <p class="mt-2 text-sm text-stone-600">Live list updates as friends join.</p>
                        </div>
                        <div class="rounded-full border border-stone-200 bg-white px-3 py-1 text-xs font-semibold text-stone-500">
                            {{ $participants->count() }} in room
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-stone-200/80 bg-stone-50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full {{ $selectedAvatar['bg'] }} {{ $selectedAvatar['text'] }}">
                                    <x-movie-avatar-icon :id="$selectedAvatar['id']" class="h-5 w-5" />
                                </span>
                                <div>
                                    <div class="text-sm font-semibold text-stone-900">{{ $name !== '' ? $name : 'You' }}</div>
                                    <div class="text-xs text-stone-500">{{ $participants->firstWhere('id', $participantId)?->is_host ? 'Host' : 'You' }}</div>
                                </div>
                            </div>
                            <div class="rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $readyBadgeClasses }}">
                                {{ $isReady ? 'Ready' : 'Preparing' }}
                            </div>
                        </div>

                        @foreach ($participants->where('id', '!=', $participantId) as $participant)
                            @php
                                $avatarData = $avatarMap[$participant->avatar] ?? $avatars[0];
                            @endphp
                            <div class="flex items-center justify-between rounded-xl border border-stone-200/80 bg-white px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full {{ $avatarData['bg'] }} {{ $avatarData['text'] }}">
                                        <x-movie-avatar-icon :id="$avatarData['id']" class="h-5 w-5" />
                                    </span>
                                    <div>
                                        <div class="text-sm font-semibold text-stone-900">{{ $participant->name ?? 'Guest' }}</div>
                                        <div class="text-xs text-stone-500">Joined</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2 text-right">
                                        <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $participant->is_ready ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-stone-200 bg-white text-stone-500' }}">
                                            {{ $participant->is_ready ? 'Ready' : 'Preparing' }}
                                        </span>
                                    </div>
                                    @if($isHost)
                                        <button
                                            type="button"
                                            x-on:click="openKick({{ $participant->id }}, @js($participant->name ?? 'Guest'))"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-rose-200/80 bg-white text-rose-500 transition hover:border-rose-300 hover:bg-rose-50"
                                            aria-label="Kick guest"
                                            title="Kick guest"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M6 6l12 12M6 18L18 6" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-xl border border-dashed border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600">
                        @if(! $hasEnoughPlayers)
                            Add at least one more player to start matching.
                        @elseif(! $everyoneReady)
                            Waiting for everyone to be ready before starting.
                        @else
                            Once everyone is here, hit start to begin matching.
                        @endif
                    </div>
                    @if($isHost)
                        <button
                            type="button"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition {{ $canStartMatching ? 'bg-amber-500 text-stone-900 shadow-amber-500/20 hover:bg-amber-400' : 'cursor-not-allowed bg-stone-200 text-stone-500 shadow-stone-200/20' }}"
                            @disabled(! $canStartMatching)
                            aria-disabled="{{ $canStartMatching ? 'false' : 'true' }}"
                        >
                            Start matching
                        </button>
                    @endif
                    <button
                        type="button"
                        wire:click="toggleReady"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition {{ $isReady ? 'bg-emerald-500 text-white shadow-emerald-500/20 hover:bg-emerald-400' : 'bg-stone-900 text-white shadow-stone-900/20 hover:bg-stone-800' }}"
                    >
                        {{ $isReady ? 'Im not ready yet' : 'Im ready' }}
                    </button>
                </div>
            </section>
        </div>

        <div
            class="fixed inset-0 z-40 flex items-center justify-center px-6"
            x-show="isVisible"
            x-cloak
            x-on:keydown.escape.window="close()"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="absolute inset-0 bg-black/40 transition-opacity duration-300 ease-out"
                x-on:click="close()"
                :class="{
                    'opacity-100': state === 'open',
                    'opacity-0': state === 'entering' || state === 'leaving'
                }"
            ></div>
            <div
                class="relative z-10 w-full max-w-md rounded-3xl border border-stone-200/80 bg-white/95 p-6 text-center shadow-[0_35px_80px_-50px_rgba(15,23,42,0.55)] transition-all duration-300 ease-out"
                :class="{
                    'translate-y-0 scale-100 opacity-100': state === 'open',
                    'translate-y-2 scale-95 opacity-0': state === 'entering' || state === 'leaving'
                }"
            >
                <div class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Confirm</div>
                <h2 class="mt-3 text-2xl font-semibold text-stone-900">Kick this guest?</h2>
                <p class="mt-2 text-sm text-stone-600">
                    You're about to yeet <span class="font-semibold text-stone-900" x-text="targetName"></span> out of the room.
                </p>
                <pre class="mt-4 text-left text-xs text-stone-500">
  (\_/)
  ( •_•)  No popcorn for you.
 / >🍿
            </pre>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-300"
                        x-on:click="close()"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:bg-rose-400"
                        x-on:click="confirmKick()"
                    >
                        Yes, kick
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
