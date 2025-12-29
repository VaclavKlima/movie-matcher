<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 right-6 h-72 w-72 rounded-full bg-amber-200/60 blur-3xl"></div>
        <div class="absolute bottom-[-8rem] left-[-6rem] h-80 w-80 rounded-full bg-teal-200/60 blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white/70 via-stone-50 to-amber-50/40"></div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-14 lg:px-10">
        <header class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                MovieMatcher
            </div>
            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">
                Match a movie in minutes.
            </h1>
            <p class="mt-4 text-lg text-stone-600">
                Create a room, invite friends, and swipe to a shared pick without the endless group chat.
            </p>
        </header>

        <section class="mt-10 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="flex flex-col gap-6">
                <div class="rounded-2xl border border-stone-200/80 bg-white/80 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-stone-900">Create a room</h2>
                            <p class="mt-2 text-sm text-stone-600">
                                Start a new match session and share the link with friends.
                            </p>
                        </div>
                        <div class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                            Host
                        </div>
                    </div>
                    <a
                        href="{{ route('rooms.create') }}"
                        class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-stone-900/10 transition hover:bg-stone-800"
                    >
                        Create room
                    </a>
                    <div class="mt-4 flex items-center gap-2 text-xs text-stone-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Generates a short code instantly
                    </div>
                </div>

                <div class="rounded-2xl border border-stone-200/80 bg-white/70 p-6 backdrop-blur">
                    <h3 class="text-lg font-semibold text-stone-900">How it works</h3>
                    <div class="mt-4 grid gap-3 text-sm text-stone-600">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full border border-stone-200 bg-white text-xs font-semibold text-stone-700">1</span>
                            <p>Pick your mood and invite friends.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full border border-stone-200 bg-white text-xs font-semibold text-stone-700">2</span>
                            <p>Swipe through quick recommendations together.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full border border-stone-200 bg-white text-xs font-semibold text-stone-700">3</span>
                            <p>Lock in a shared movie choice in one tap.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-900">Join a room</h2>
                        <p class="mt-2 text-sm text-stone-600">
                            Enter the short code from your friend to jump in.
                        </p>
                    </div>
                    <div class="rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                        Guest
                    </div>
                </div>

                <div class="mt-6" x-data="{ code: '' }">
                    <label class="text-sm font-semibold text-stone-700" for="room-code">Room code</label>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                        <input
                            id="room-code"
                            type="text"
                            inputmode="text"
                            autocomplete="off"
                            placeholder="4F9K2A"
                            x-model="code"
                            x-on:input="code = code.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6)"
                            class="h-12 w-full rounded-xl border border-stone-200 bg-stone-50 px-4 text-lg font-semibold tracking-[0.3em] text-stone-800 shadow-inner outline-none transition focus:border-stone-400 focus:bg-white"
                        />
                        <button
                            type="button"
                            :disabled="code.length < 4"
                            class="inline-flex h-12 items-center justify-center rounded-xl bg-teal-600 px-5 text-sm font-semibold text-white shadow-lg shadow-teal-600/20 transition hover:bg-teal-500 disabled:cursor-not-allowed disabled:bg-teal-300"
                            x-on:click="if (code.length >= 4) { window.location = '{{ url('/rooms') }}/' + code + '/join' }"
                        >
                            Join room
                        </button>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-stone-500">
                        <span>Codes are 4-6 characters.</span>
                        <span class="rounded-full border border-stone-200 bg-white px-3 py-1 text-[11px] font-semibold tracking-[0.2em] text-stone-700" x-cloak x-text="code.length ? code : '----'"></span>
                    </div>
                </div>

                <div class="mt-8 rounded-xl border border-dashed border-stone-200 bg-stone-50 p-4 text-sm text-stone-600">
                    Tip: keep the room open while friends join so no one misses the first vote.
                </div>
            </div>
        </section>
    </div>
</div>
