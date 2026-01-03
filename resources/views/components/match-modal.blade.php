@props(['show' => false, 'matchedMovie' => null, 'isHost' => false])

@if ($show)
<div
    x-data="{
        showBackdrop: false,
        leftCurtainTransform: 'translateX(0)',
        rightCurtainTransform: 'translateX(0)',
        showContent: false,
        hasAnimated: false,

        init() {
            // Check if this is a fresh open (not a page reload)
            const isPageReload = @js($show);

            if (isPageReload && !this.hasAnimated) {
                // Trigger animation sequence
                this.startAnimation();
            }
        },

        startAnimation() {
            if (this.hasAnimated) return;

            this.hasAnimated = true;
            this.showBackdrop = false;
            this.showContent = false;

            // Start with curtains closed (at center)
            this.leftCurtainTransform = 'translateX(0)';
            this.rightCurtainTransform = 'translateX(0)';

            requestAnimationFrame(() => {
                // Step 1: Show backdrop
                this.showBackdrop = true;

                // Step 2: After 200ms, animate curtains opening
                setTimeout(() => {
                    // Open curtains: left goes left, right goes right
                    this.leftCurtainTransform = 'translateX(-100%)';
                    this.rightCurtainTransform = 'translateX(100%)';

                    // Step 3: After curtains open (800ms), show content
                    setTimeout(() => {
                        this.showContent = true;
                    }, 800);
                }, 200);
            });
        }
    }"
    x-init="startAnimation()"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-4 sm:px-6"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity duration-500"
        x-show="showBackdrop"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    ></div>

    {{-- Curtain Effect --}}
    <div
        class="absolute inset-0 overflow-hidden pointer-events-none"
    >
        <div
            class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-red-900 to-red-800 transition-transform duration-[800ms] ease-out"
            :style="'transform: ' + leftCurtainTransform"></div>
        <div
            class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-red-900 to-red-800 transition-transform duration-[800ms] ease-out"
            :style="'transform: ' + rightCurtainTransform"></div>
    </div>

    {{-- Modal Content --}}
    <div
        class="relative z-10 w-full max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto text-center"
        x-show="showContent"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    >
        {{-- Confetti Effect --}}
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
            <div class="animate-confetti text-4xl sm:text-6xl" style="animation-delay: 0s;">🎉</div>
            <div class="animate-confetti text-4xl sm:text-6xl" style="animation-delay: 0.2s;">⭐</div>
            <div class="animate-confetti text-4xl sm:text-6xl" style="animation-delay: 0.4s;">🎊</div>
        </div>

        <div class="relative rounded-2xl sm:rounded-3xl border-2 border-amber-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-4 sm:p-8 shadow-2xl shadow-amber-500/30 backdrop-blur-xl">
            {{-- Header --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/50 bg-emerald-500/20 px-3 py-1.5 sm:px-4 sm:py-2 text-[0.65rem] sm:text-xs font-bold uppercase tracking-[0.2em] sm:tracking-[0.3em] text-emerald-300">
                <span class="animate-ping text-base sm:text-lg">🎯</span>
                <span>It's a Match!</span>
            </div>

            <h2 class="mt-4 sm:mt-6 text-2xl sm:text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200 drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                Everyone Said YES! 🎉
            </h2>

            <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-purple-200/90 px-2">
                The crowd has spoken. Time to grab the popcorn and dim the lights!
            </p>

            <div class="mt-4 sm:mt-6 flex items-center justify-center gap-3 sm:gap-4 text-3xl sm:text-4xl">
                <span class="animate-bounce">🎬</span>
                <span class="animate-pulse text-4xl sm:text-5xl">🍿</span>
                <span class="animate-bounce" style="animation-delay: 0.1s;">⭐</span>
            </div>

            @if ($matchedMovie)
                <div class="mt-6 sm:mt-8 overflow-hidden rounded-xl sm:rounded-2xl border-2 border-slate-700/50 bg-gradient-to-br from-slate-900 to-slate-800 text-left shadow-2xl">
                    {{-- Poster --}}
                    <div class="film-strip-border relative flex h-48 sm:h-64 md:h-72 w-full items-center justify-center bg-gradient-to-br from-slate-950 to-slate-900 p-3 sm:p-4">
                        @if ($matchedMovie->poster_url)
                            <img
                                src="{{ $matchedMovie->poster_url }}"
                                alt="{{ $matchedMovie->name }}"
                                class="h-full w-full object-contain drop-shadow-2xl"
                            />
                        @else
                            <div class="flex flex-col items-center gap-3 text-purple-300/60">
                                <span class="text-6xl">🎬</span>
                                <span class="text-sm font-semibold">A masterpiece awaits</span>
                            </div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                        <div class="flex items-center gap-2 sm:gap-3 rounded-lg border border-amber-400/20 bg-slate-950/50 px-3 sm:px-4 py-2">
                            <span class="text-base sm:text-lg">🎬</span>
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[0.65rem] sm:text-xs font-bold uppercase tracking-[0.15em] sm:tracking-[0.2em] text-amber-300/90">
                                <span>{{ $matchedMovie->year }}</span>
                                <span class="text-amber-400/50">•</span>
                                <span>{{ $matchedMovie->duration }}</span>
                                <span class="text-amber-400/50">•</span>
                                <span>{{ $matchedMovie->country }}</span>
                            </div>
                        </div>

                        <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-amber-50">{{ $matchedMovie->name }}</h3>

                        <p class="line-clamp-3 text-xs sm:text-sm leading-relaxed text-purple-100/80">
                            {{ $matchedMovie->description }}
                        </p>

                        @if ($matchedMovie->actors->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($matchedMovie->actors as $actor)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-purple-400/30 bg-gradient-to-r from-purple-500/20 to-purple-600/10 px-3 py-1 text-xs font-semibold text-purple-200">
                                        ⭐ {{ $actor->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-6 sm:mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4">
                <button
                    type="button"
                    wire:click="continueHunting"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl sm:rounded-2xl border-2 border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/30 px-6 sm:px-8 py-3 sm:py-4 text-sm sm:text-base font-bold text-amber-100 shadow-2xl shadow-amber-500/30 transition-all duration-300 hover:scale-105 hover:border-amber-400 hover:from-amber-500/40 hover:to-amber-600/40 hover:shadow-amber-500/50 active:scale-95"
                >
                    <span>🎬</span>
                    <span>Find Another Gem</span>
                </button>

                @if ($isHost && $matchedMovie)
                    <button
                        type="button"
                        wire:click="endRoomWithMatch({{ $matchedMovie->id }})"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl sm:rounded-2xl border-2 border-rose-400/50 bg-gradient-to-r from-rose-500/30 to-rose-600/30 px-6 sm:px-8 py-3 sm:py-4 text-sm sm:text-base font-bold text-rose-100 shadow-2xl shadow-rose-500/30 transition-all duration-300 hover:scale-105 hover:border-rose-400 hover:from-rose-500/40 hover:to-rose-600/40 hover:shadow-rose-500/50 active:scale-95"
                    >
                        <span>🎯</span>
                        <span>Roll Credits &amp; View Stats</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
