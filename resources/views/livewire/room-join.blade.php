@php
    $avatarMap = [];
    foreach ($avatars as $avatarOption) {
        $avatarMap[$avatarOption['id']] = $avatarOption;
    }
    $selectedAvatar = $avatarMap[$avatar] ?? $avatars[0];
@endphp

<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900">
    {{-- Cinema Background Effects --}}
    <div class="pointer-events-none absolute inset-0">
        {{-- Film reel decorative elements --}}
        <div class="absolute -right-32 top-20 h-64 w-64 animate-film-reel rounded-full border-8 border-amber-400/20 opacity-20"></div>
        <div class="absolute -left-32 bottom-40 h-96 w-96 animate-film-reel rounded-full border-8 border-emerald-400/20 opacity-10" style="animation-delay: -10s;"></div>

        {{-- Spotlight effects --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="animate-spotlight absolute inset-y-0 w-1/3 bg-gradient-to-r from-transparent via-emerald-300/10 to-transparent"></div>
        </div>

        {{-- Gradient overlays --}}
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(16,185,129,0.12),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(139,92,246,0.12),transparent_50%)]"></div>

        {{-- Stars/lights --}}
        <div class="absolute inset-0">
            <div class="animate-marquee-lights absolute left-[10%] top-[15%] h-2 w-2 rounded-full bg-emerald-300"></div>
            <div class="animate-marquee-lights absolute left-[85%] top-[25%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 0.5s;"></div>
            <div class="animate-marquee-lights absolute left-[60%] top-[70%] h-2 w-2 rounded-full bg-emerald-300" style="animation-delay: 1s;"></div>
            <div class="animate-marquee-lights absolute left-[20%] top-[80%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 1.5s;"></div>
        </div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-3xl flex-col justify-center px-6 py-14 lg:px-10">
        <header class="text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2.5 rounded-full border-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/20 to-emerald-400/10 px-5 py-2.5 text-xs font-bold uppercase tracking-[0.25em] text-emerald-300 shadow-lg shadow-emerald-500/20 backdrop-blur-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                </span>
                Guest Entry
            </div>

            {{-- Title --}}
            <div class="relative mt-6">
                <h1 class="relative text-4xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 via-emerald-100 to-emerald-200 sm:text-5xl drop-shadow-[0_0_30px_rgba(16,185,129,0.3)]">
                    🎫 Get Your Ticket
                </h1>
                <div class="mt-2 flex justify-center gap-1">
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-emerald-400"></span>
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-purple-400" style="animation-delay: 0.3s;"></span>
                    <span class="h-1 w-12 animate-marquee-lights rounded-full bg-emerald-400" style="animation-delay: 0.6s;"></span>
                </div>
            </div>

            <p class="mt-6 text-lg text-purple-200/90">
                Grab your seat by picking a name and avatar. The show's about to start!
            </p>
        </header>

        <form
            wire:submit="confirmJoin"
            class="animate-card-slide mt-10 rounded-3xl border-2 border-emerald-400/30 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-8 shadow-2xl shadow-emerald-500/20 backdrop-blur-xl"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-amber-100 drop-shadow-lg">🎬 Room {{ $roomCode }}</h2>
                    <p class="mt-2 text-sm text-purple-200/80">You can tweak your profile after joining too.</p>
                </div>
                <div class="rounded-full border-2 border-emerald-400/50 bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 px-4 py-2 text-xs font-bold text-emerald-200 shadow-lg shadow-emerald-500/20">
                    Guest
                </div>
            </div>

            {{-- Display Name --}}
            <div class="mt-8">
                <label class="text-sm font-bold text-amber-200" for="display-name">✍️ Display Name</label>
                <input
                    id="display-name"
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    placeholder="Enter your name"
                    class="mt-3 h-12 w-full rounded-xl border-2 border-purple-400/30 bg-slate-950/50 px-5 text-base font-semibold text-amber-100 shadow-inner outline-none transition placeholder:text-purple-400/40 focus:border-emerald-400/50 focus:bg-slate-900/70 focus:ring-2 focus:ring-emerald-400/20"
                />
                @error('name')
                    <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-rose-300">
                        <span>⚠️</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            {{-- Avatar Selection --}}
            <div class="mt-8">
                <div class="text-sm font-bold text-amber-200">🎭 Choose Your Character</div>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($avatars as $avatarOption)
                        @php
                            $isSelected = $avatar === $avatarOption['id'];
                        @endphp
                        <button
                            type="button"
                            wire:click="$set('avatar', '{{ $avatarOption['id'] }}')"
                            class="group relative flex items-center gap-3 overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $isSelected ? 'border-'.$avatarOption['ring'].' border-opacity-100 bg-gradient-to-br from-slate-800 to-slate-700 shadow-lg '.$avatarOption['ring'].' ring-2' : 'border-slate-600/50 bg-gradient-to-r from-slate-800/70 to-slate-700/70 hover:border-purple-400/50' }} p-3"
                        >
                            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full shadow-lg {{ $avatarOption['bg'] }} {{ $avatarOption['text'] }}">
                                <x-movie-avatar-icon :id="$avatarOption['id']" class="h-6 w-6" />
                            </span>
                            <span class="text-sm font-bold text-amber-50">{{ $avatarOption['label'] }}</span>
                            @if($isSelected)
                                <span class="absolute right-2 top-2 text-emerald-400">✓</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                @error('avatar')
                    <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-rose-300">
                        <span>⚠️</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            {{-- Preview & Submit --}}
            <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Ticket Preview --}}
                <div class="ticket-stub flex items-center gap-4 rounded-r-xl border-l-4 border-emerald-400/60 bg-gradient-to-r from-emerald-500/20 to-slate-800/50 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full shadow-lg {{ $selectedAvatar['bg'] }} {{ $selectedAvatar['text'] }}">
                        <x-movie-avatar-icon :id="$selectedAvatar['id']" class="h-6 w-6" />
                    </span>
                    <div>
                        <div class="font-bold text-amber-50">{{ $name !== '' ? $name : 'Guest' }}</div>
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">Preview</div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="group/btn relative inline-flex h-14 flex-shrink-0 items-center justify-center gap-2 overflow-hidden rounded-2xl border-2 border-emerald-400/50 bg-gradient-to-r from-emerald-500/30 to-emerald-600/30 px-8 text-base font-bold text-emerald-100 shadow-2xl shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:border-emerald-400 hover:from-emerald-500/40 hover:to-emerald-600/40 hover:shadow-emerald-500/50 active:scale-95"
                >
                    <span class="relative z-10 flex items-center gap-2">
                        <span>🚪</span>
                        <span>Enter Room</span>
                    </span>
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                </button>
            </div>
        </form>

        {{-- Info Box --}}
        <div class="mt-6 flex items-start gap-3 rounded-xl border border-dashed border-purple-400/30 bg-purple-500/10 px-5 py-4 text-sm backdrop-blur-sm">
            <span class="text-xl">🎪</span>
            <div>
                <p class="font-semibold text-purple-200">Welcome to the Show!</p>
                <p class="mt-1 text-xs text-purple-300/80">Once you enter, you'll be in the lobby waiting for the host to start the movie matching experience.</p>
            </div>
        </div>
    </div>
</div>
