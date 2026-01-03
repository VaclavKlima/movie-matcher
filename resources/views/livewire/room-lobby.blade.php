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
        ? 'border-emerald-400/50 bg-emerald-500/20 text-emerald-300'
        : 'border-slate-600/50 bg-slate-700/30 text-slate-400';
@endphp

<div class="relative min-h-screen" wire:poll.5s="refreshParticipants" x-data="kickModal(@this)">
    @if ($isKicked)
        {{-- Kicked State --}}
        <div class="relative mx-auto flex min-h-screen max-w-3xl flex-col items-center justify-center gap-6 px-6 py-16 text-center">
            <div class="animate-card-slide rounded-3xl border-2 border-rose-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 px-10 py-12 shadow-2xl shadow-rose-500/30 backdrop-blur-xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-rose-400/50 bg-rose-500/20 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-rose-300">
                    <span>🚫</span>
                    <span>Access Denied</span>
                </div>

                <h1 class="mt-6 text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-200 via-rose-100 to-rose-200 drop-shadow-[0_0_30px_rgba(244,63,94,0.3)] sm:text-5xl">
                    Theater Closed
                </h1>

                <p class="mt-4 text-lg text-purple-200/90">
                    The bouncer says: "No ticket, no popcorn, no show."
                </p>

                <div class="mt-6 flex justify-center">
                    <pre class="text-left text-sm text-purple-300/70 font-mono">
      .-"""-.
     / -   -  \
    |  .-. .- |
    |  \o| |o (
    \     ^    \
     '.  )--'  /
       '-...-'   POOF!</pre>
                </div>

                <a
                    href="{{ route('home') }}"
                    class="group/btn relative mt-8 inline-flex items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-8 py-4 text-base font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    <span class="relative z-10 flex items-center gap-2">
                        <span>🏠</span>
                        <span>Back to Home</span>
                    </span>
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                </a>
            </div>
        </div>
    @else
        {{-- Active Lobby State --}}
        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-10 px-6 py-14 lg:px-10">
            {{-- Header --}}
            <header class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-4">
                    {{-- Status Badge --}}
                    <div class="inline-flex items-center gap-2.5 rounded-full border-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/20 to-emerald-400/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.25em] text-emerald-300 shadow-lg shadow-emerald-500/20 backdrop-blur-sm">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        </span>
                        Doors Open
                    </div>

                    {{-- Title --}}
                    <div class="relative">
                        <h1 class="relative text-4xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 sm:text-5xl drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                            🎭 The Theater Lobby
                        </h1>
                        <div class="mt-1 flex gap-1">
                            <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400"></span>
                            <span class="h-1 w-12 animate-marquee-lights rounded-full bg-purple-400" style="animation-delay: 0.3s;"></span>
                            <span class="h-1 w-12 animate-marquee-lights rounded-full bg-amber-400" style="animation-delay: 0.6s;"></span>
                        </div>
                    </div>

                    <p class="text-lg text-purple-200/90 max-w-2xl">
                        🍿 Share the code so everyone can grab their seats before showtime!
                    </p>
                </div>

                {{-- Room Code Card --}}
                <div class="group rounded-2xl border-2 border-amber-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-6 shadow-2xl shadow-amber-500/20 backdrop-blur-xl transition-all duration-300 hover:border-amber-400/50 hover:shadow-amber-500/30">
                    <div class="text-xs font-bold uppercase tracking-[0.25em] text-amber-300/80">Screening Room</div>
                    <div
                        id="room-code-panel"
                        class="mt-3 flex items-center gap-2"
                        x-data="roomCodePanel('Code copied!')"
                        x-on:show-room-code.window="showCode = true"
                    >
                        <div class="flex items-center gap-2 rounded-xl border border-amber-400/20 bg-slate-950/50 px-4 py-2.5">
                            <span class="text-xl">🎫</span>
                            <span class="text-xl font-black tracking-[0.4em] text-amber-200" x-text="showCode ? '{{ $roomCode }}' : '●●●●●●'"></span>
                        </div>
                        <button
                            type="button"
                            class="rounded-lg border border-slate-600/50 bg-slate-800/50 px-3 py-2 text-xs font-bold text-slate-300 transition hover:border-amber-400/30 hover:bg-slate-700/50 hover:text-amber-200"
                            x-on:click="showCode = !showCode"
                            x-text="showCode ? 'Hide' : 'Show'"
                        ></button>
                        <button
                            type="button"
                            class="rounded-lg border border-slate-600/50 bg-slate-800/50 px-3 py-2 text-xs font-bold text-slate-300 transition hover:border-emerald-400/30 hover:bg-slate-700/50 hover:text-emerald-200"
                            x-on:click="copy('{{ $roomCode }}')"
                        >
                            Copy
                        </button>
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

            <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                {{-- Left Column --}}
                <div class="flex flex-col gap-6">
                    {{-- Share Card --}}
                    <div class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">📤 Share This Room</h2>
                                <p class="mt-2 text-sm text-purple-200/80">Send the invite link to friends and keep the room open.</p>
                            </div>
                            <div class="animate-pulse-glow rounded-full border-2 border-amber-400/50 bg-gradient-to-br from-amber-500/20 to-amber-600/20 px-4 py-2 text-xs font-bold text-amber-200 shadow-lg shadow-amber-500/20">
                                Host
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-600/50 bg-slate-950/50 px-4 py-3 text-sm font-mono text-amber-200/90">
                            {{ $shareUrl }}
                        </div>

                        <div
                            class="mt-4 flex flex-wrap items-center gap-3"
                            x-data="clipboardHelper('Link copied!')"
                        >
                            <button
                                type="button"
                                class="group/btn relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-xl border-2 border-purple-400/50 bg-gradient-to-r from-purple-500/30 to-purple-600/30 px-5 py-2.5 text-sm font-bold text-purple-100 shadow-lg shadow-purple-500/20 transition-all duration-300 hover:scale-105 hover:border-purple-400 hover:from-purple-500/40 hover:to-purple-600/30 active:scale-95"
                                x-on:click="copy('{{ $shareUrl }}')"
                            >
                                <span class="relative z-10">📋 Share Link</span>
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-600/50 bg-slate-800/50 px-4 py-2.5 text-sm font-bold text-slate-300 transition hover:border-emerald-400/30 hover:bg-slate-700/50 hover:text-emerald-200"
                                x-on:click="$dispatch('show-room-code'); document.getElementById('room-code-panel')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                            >
                                👁️ Show Code
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-600/50 bg-slate-800/50 px-4 py-2.5 text-sm font-bold text-slate-300 transition hover:border-amber-400/30 hover:bg-slate-700/50 hover:text-amber-200"
                                x-on:click="copy('{{ $roomCode }}')"
                            >
                                📝 Copy Code
                            </button>
                        </div>
                    </div>

                    {{-- Profile Card --}}
                    <div class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/70 to-slate-900/70 p-6 backdrop-blur-xl">
                        <h2 class="text-xl font-black text-amber-100 drop-shadow-lg">🎨 Your Profile</h2>
                        <p class="mt-2 text-sm text-purple-200/80">Customize your name and movie-themed avatar.</p>

                        <div class="mt-5">
                            <label class="text-sm font-bold text-amber-200" for="display-name">Display Name</label>
                            <input
                                id="display-name"
                                type="text"
                                wire:model.live.debounce.500ms="name"
                                placeholder="Enter your name"
                                class="mt-2 h-11 w-full rounded-xl border-2 border-purple-400/30 bg-slate-950/50 px-4 text-base font-semibold text-amber-100 shadow-inner outline-none transition placeholder:text-purple-400/40 focus:border-purple-400/50 focus:bg-slate-900/70 focus:ring-2 focus:ring-purple-400/20"
                            />
                        </div>

                        <div class="mt-6">
                            <div class="text-sm font-bold text-amber-200">Choose Avatar</div>
                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach ($avatars as $avatarOption)
                                    @php
                                        $isSelected = $avatar === $avatarOption['id'];
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="$set('avatar', '{{ $avatarOption['id'] }}')"
                                        class="group relative flex items-center gap-2.5 overflow-hidden rounded-xl border-2 p-3 transition-all duration-300 {{ $isSelected ? 'border-'.$avatarOption['ring'].' bg-gradient-to-br from-slate-800 to-slate-700 shadow-lg '.$avatarOption['ring'].' ring-2' : 'border-slate-600/50 bg-gradient-to-r from-slate-800/70 to-slate-700/70 hover:border-purple-400/50' }}"
                                    >
                                        <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full shadow-lg {{ $avatarOption['bg'] }} {{ $avatarOption['text'] }}">
                                            <x-movie-avatar-icon :id="$avatarOption['id']" class="h-5 w-5" />
                                        </span>
                                        <span class="text-xs font-bold text-amber-50">{{ $avatarOption['label'] }}</span>
                                        @if($isSelected)
                                            <span class="absolute right-2 top-2 text-emerald-400">✓</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Participants Card --}}
                <div class="rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-800/80 to-slate-900/80 p-6 shadow-2xl shadow-purple-900/40 backdrop-blur-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎬 In Theater</h2>
                            <p class="mt-2 text-sm text-purple-200/80">Everyone seated and waiting</p>
                        </div>
                        <div class="rounded-full border-2 border-purple-400/50 bg-gradient-to-br from-purple-500/20 to-purple-600/20 px-4 py-2 text-xs font-bold text-purple-200 shadow-lg shadow-purple-500/20">
                            {{ $participants->count() }} 👥
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        {{-- Current User --}}
                        <div class="animate-float flex items-center justify-between rounded-xl border-2 border-amber-400/30 bg-gradient-to-r from-amber-500/10 to-slate-800/70 px-4 py-3.5 shadow-lg">
                            <div class="flex items-center gap-3">
                                <span class="cinema-seat flex h-12 w-12 items-center justify-center bg-gradient-to-br from-amber-500 to-amber-700 text-xl font-black text-white shadow-lg">
                                    <x-movie-avatar-icon :id="$selectedAvatar['id']" class="h-6 w-6" />
                                </span>
                                <div>
                                    <div class="font-bold text-amber-50">{{ $name !== '' ? $name : 'You' }}</div>
                                    <div class="text-xs text-amber-300/80">{{ $participants->firstWhere('id', $participantId)?->is_host ? '👑 Host' : '🎫 You' }}</div>
                                </div>
                            </div>
                            <span class="flex items-center gap-1.5 rounded-full border-2 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.15em] {{ $readyBadgeClasses }}">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full {{ $isReady ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                                {{ $isReady ? 'Ready' : 'Preparing' }}
                            </span>
                        </div>

                        {{-- Other Participants --}}
                        @foreach ($participants->where('id', '!=', $participantId) as $index => $participant)
                            @php
                                $avatarData = $avatarMap[$participant->avatar] ?? $avatars[0];
                            @endphp
                            <div class="animate-float group flex items-center justify-between rounded-xl border border-slate-600/50 bg-gradient-to-r from-slate-800/90 to-slate-700/80 px-4 py-3.5 shadow-lg transition-all duration-300 hover:border-purple-400/50 hover:shadow-purple-500/20"
                                 style="animation-delay: {{ ($index + 1) * 0.1 }}s;">
                                <div class="flex items-center gap-3">
                                    <span class="cinema-seat flex h-12 w-12 items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700 text-xl font-black text-white shadow-lg">
                                        <x-movie-avatar-icon :id="$avatarData['id']" class="h-6 w-6" />
                                    </span>
                                    <div>
                                        <div class="font-bold text-amber-50">{{ $participant->name ?? 'Guest' }}</div>
                                        <div class="text-xs text-purple-300/80">{{ $participant->is_host ? '👑 Host' : '🎫 Viewer' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] {{ $participant->is_ready ? 'border-emerald-400/50 bg-emerald-500/20 text-emerald-300' : 'border-slate-600/50 bg-slate-700/30 text-slate-400' }}">
                                        <span class="h-1.5 w-1.5 animate-pulse rounded-full {{ $participant->is_ready ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                                        {{ $participant->is_ready ? 'Ready' : 'Preparing' }}
                                    </span>
                                    @if($isHost)
                                        <button
                                            type="button"
                                            x-on:click="openKick({{ $participant->id }}, @js($participant->name ?? 'Guest'))"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-400/50 bg-rose-500/20 text-rose-300 transition hover:border-rose-400 hover:bg-rose-500/30 hover:scale-110"
                                            aria-label="Kick guest"
                                            title="Kick {{ $participant->name ?? 'Guest' }}"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 6L6 18M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Status Message --}}
                    <div class="mt-6 flex items-start gap-3 rounded-xl border border-dashed border-purple-400/30 bg-purple-500/10 px-4 py-3 text-sm backdrop-blur-sm">
                        <span class="text-xl">
                            @if(! $hasEnoughPlayers)
                                ⏳
                            @elseif(! $everyoneReady)
                                🍿
                            @else
                                ✨
                            @endif
                        </span>
                        <div>
                            <p class="font-semibold text-purple-200">
                                @if(! $hasEnoughPlayers)
                                    Need more viewers
                                @elseif(! $everyoneReady)
                                    Waiting for ready status
                                @else
                                    All set! Start the show
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-purple-300/80">
                                @if(! $hasEnoughPlayers)
                                    At least 2 people needed to start matching
                                @elseif(! $everyoneReady)
                                    Everyone needs to mark themselves as ready
                                @else
                                    Host can launch the matching experience
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    @if($isHost)
                        <button
                            type="button"
                            wire:click="startMatching"
                            class="group/btn relative mt-4 inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 px-6 py-4 text-base font-bold shadow-2xl transition-all duration-300 {{ $canStartMatching ? 'border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 text-amber-100 shadow-amber-500/30 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95' : 'cursor-not-allowed border-slate-600/50 bg-gradient-to-r from-slate-700/30 to-slate-800/30 text-slate-400 shadow-none' }}"
                            @disabled(! $canStartMatching)
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                <span>🎬</span>
                                <span>Start the Show</span>
                            </span>
                            @if($canStartMatching)
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                            @endif
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="toggleReady"
                            class="group/btn relative mt-4 inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 px-6 py-4 text-base font-bold shadow-2xl transition-all duration-300 {{ $isReady ? 'border-purple-400/50 bg-gradient-to-r from-purple-500/30 to-purple-600/30 text-purple-100 shadow-purple-500/30 hover:scale-105 hover:border-purple-400 hover:from-purple-500/40 hover:to-purple-600/40 hover:shadow-purple-500/50' : 'border-emerald-400/50 bg-gradient-to-r from-emerald-500/30 to-emerald-600/30 text-emerald-100 shadow-emerald-500/30 hover:scale-105 hover:border-emerald-400 hover:from-emerald-500/40 hover:to-emerald-600/40 hover:shadow-emerald-500/50' }} active:scale-95"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                <span>{{ $isReady ? '⏸️' : '✅' }}</span>
                                <span>{{ $isReady ? 'Not Ready Yet' : 'I\'m Ready!' }}</span>
                            </span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                        </button>
                    @endif
                </div>
            </section>
        </div>

        {{-- Kick Modal --}}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center px-6"
            x-show="isVisible"
            x-cloak
            x-on:keydown.escape.window="close()"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity duration-300"
                x-on:click="close()"
                :class="{
                    'opacity-100': state === 'open',
                    'opacity-0': state === 'entering' || state === 'leaving'
                }"
            ></div>
            <div
                class="relative z-10 w-full max-w-md rounded-3xl border-2 border-rose-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-8 text-center shadow-2xl shadow-rose-500/30 backdrop-blur-xl transition-all duration-300"
                :class="{
                    'translate-y-0 scale-100 opacity-100': state === 'open',
                    'translate-y-4 scale-95 opacity-0': state === 'entering' || state === 'leaving'
                }"
            >
                <div class="inline-flex items-center gap-2 rounded-full border border-rose-400/50 bg-rose-500/20 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-rose-300">
                    <span>🚫</span>
                    <span>Confirm</span>
                </div>

                <h2 class="mt-6 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-200 via-rose-100 to-rose-200">
                    Kick This Guest?
                </h2>

                <p class="mt-3 text-base text-purple-200/90">
                    You're about to eject <span class="font-bold text-amber-200" x-text="targetName"></span> from the theater.
                </p>

                <div class="mt-6 flex justify-center">
                    <pre class="text-left text-xs text-purple-300/70 font-mono">
  (\_/)
  ( •_•)  No popcorn for you.
 / >🍿</pre>
                </div>

                <div class="mt-8 flex items-center justify-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-600/50 bg-slate-800/50 px-5 py-3 text-sm font-bold text-slate-300 transition hover:border-slate-500 hover:bg-slate-700/50"
                        x-on:click="close()"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="group/btn relative inline-flex items-center gap-2 overflow-hidden rounded-xl border-2 border-rose-400/50 bg-gradient-to-r from-rose-500/30 to-rose-600/30 px-5 py-3 text-sm font-bold text-rose-100 shadow-lg shadow-rose-500/30 transition-all duration-300 hover:scale-105 hover:border-rose-400 hover:from-rose-500/40 hover:to-rose-600/40 active:scale-95"
                        x-on:click="confirmKick()"
                    >
                        <span class="relative z-10 flex items-center gap-2">
                            <span>👢</span>
                            <span>Yes, Kick</span>
                        </span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
