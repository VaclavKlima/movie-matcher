@php
    $avatarMap = [];
    foreach ($avatars as $avatarOption) {
        $avatarMap[$avatarOption['id']] = $avatarOption;
    }
    $selectedAvatar = $avatarMap[$avatar] ?? $avatars[0];
@endphp

<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 right-6 h-72 w-72 rounded-full bg-amber-200/60 blur-3xl"></div>
        <div class="absolute bottom-[-8rem] left-[-6rem] h-80 w-80 rounded-full bg-teal-200/60 blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white/70 via-stone-50 to-amber-50/40"></div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-3xl flex-col justify-center px-6 py-14 lg:px-10">
        <header class="text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                Join Room
            </div>
            <h1 class="mt-6 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                Confirm your spot before entering.
            </h1>
            <p class="mt-4 text-base text-stone-600">
                Pick a display name and avatar so the host knows who just joined.
            </p>
        </header>

        <form
            wire:submit="confirmJoin"
            class="mt-10 rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-900">Room {{ $roomCode }}</h2>
                    <p class="mt-2 text-sm text-stone-600">You can tweak your name and avatar after joining too.</p>
                </div>
                <div class="rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                    Guest
                </div>
            </div>

            <div class="mt-6">
                <label class="text-sm font-semibold text-stone-700" for="display-name">Display name</label>
                <input
                    id="display-name"
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    class="mt-2 h-11 w-full rounded-xl border border-stone-200 bg-stone-50 px-4 text-sm text-stone-800 shadow-inner outline-none transition focus:border-stone-400 focus:bg-white"
                />
                @error('name')
                    <div class="mt-2 text-xs font-semibold text-rose-500">{{ $message }}</div>
                @enderror
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
                @error('avatar')
                    <div class="mt-2 text-xs font-semibold text-rose-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 rounded-xl border border-dashed border-stone-200 bg-stone-50 px-4 py-3 text-xs text-stone-600">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full {{ $selectedAvatar['bg'] }} {{ $selectedAvatar['text'] }}">
                        <x-movie-avatar-icon :id="$selectedAvatar['id']" class="h-4 w-4" />
                    </span>
                    <div>
                        <div class="font-semibold text-stone-700">{{ $name !== '' ? $name : 'Guest' }}</div>
                        <div class="text-[11px] uppercase tracking-[0.2em] text-stone-400">Preview</div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-stone-900/15 transition hover:bg-stone-800"
                >
                    Enter room
                </button>
            </div>
        </form>
    </div>
</div>
