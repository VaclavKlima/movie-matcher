<div
    wire:ignore
    x-data
    x-on:toast.window="$store.toasts.push($event.detail.message, $event.detail.type || 'info')"
    class="pointer-events-none fixed inset-x-0 top-6 z-50 flex justify-center px-4"
>
    <div class="flex w-full max-w-md flex-col gap-3">
        <template x-for="toast in $store.toasts.list" :key="toast.id">
            <div
                class="pointer-events-auto relative overflow-hidden rounded-2xl border-2 px-5 py-4 text-sm font-bold shadow-2xl backdrop-blur-xl transition-all duration-300 ease-out"
                :class="{
                    // Type-based styling - Cinema Theme
                    'border-emerald-400/50 bg-gradient-to-r from-emerald-500/30 to-emerald-600/20 text-emerald-100 shadow-emerald-500/30': toast.type === 'success',
                    'border-amber-400/50 bg-gradient-to-r from-amber-500/30 to-amber-600/20 text-amber-100 shadow-amber-500/30': toast.type === 'warning',
                    'border-rose-400/50 bg-gradient-to-r from-rose-500/30 to-rose-600/20 text-rose-100 shadow-rose-500/30': toast.type === 'error',
                    'border-purple-400/50 bg-gradient-to-r from-purple-500/30 to-purple-600/20 text-purple-100 shadow-purple-500/30': toast.type === 'info',
                    // Animation states
                    'translate-y-0 scale-100 opacity-100 rotate-0': toast.state === 'visible',
                    '-translate-y-6 scale-95 opacity-0 rotate-2': toast.state === 'entering',
                    '-translate-y-3 scale-95 opacity-0 -rotate-1': toast.state === 'leaving'
                }"
            >
                {{-- Background Gradient Overlay --}}
                <div
                    class="absolute inset-0 opacity-50 transition-opacity duration-300"
                    :class="{
                        'bg-gradient-to-r from-emerald-400/20 via-transparent to-emerald-300/10': toast.type === 'success',
                        'bg-gradient-to-r from-amber-400/20 via-transparent to-amber-300/10': toast.type === 'warning',
                        'bg-gradient-to-r from-rose-400/20 via-transparent to-rose-300/10': toast.type === 'error',
                        'bg-gradient-to-r from-purple-400/20 via-transparent to-purple-300/10': toast.type === 'info'
                    }"
                ></div>

                {{-- Left Accent Bar with Glow --}}
                <div
                    class="absolute left-0 top-0 h-full w-1 animate-pulse"
                    :class="{
                        'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.6)]': toast.type === 'success',
                        'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.6)]': toast.type === 'warning',
                        'bg-rose-400 shadow-[0_0_8px_rgba(251,113,113,0.6)]': toast.type === 'error',
                        'bg-purple-400 shadow-[0_0_8px_rgba(192,132,252,0.6)]': toast.type === 'info'
                    }"
                ></div>

                {{-- Shimmer Effect on Hover --}}
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-700 group-hover:translate-x-full"></div>

                {{-- Content --}}
                <div class="relative flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        {{-- Icon --}}
                        <span class="flex-shrink-0 text-xl" x-html="{
                            'success': '✅',
                            'warning': '⚠️',
                            'error': '❌',
                            'info': '🎬'
                        }[toast.type]"></span>

                        {{-- Message --}}
                        <span class="font-semibold" x-text="toast.message"></span>
                    </div>

                    {{-- Close Button --}}
                    <button
                        type="button"
                        class="flex-shrink-0 rounded-lg px-2 py-1 text-xs font-bold uppercase tracking-[0.15em] transition-all duration-200 hover:scale-110 active:scale-95"
                        :class="{
                            'text-emerald-200 hover:bg-emerald-400/20': toast.type === 'success',
                            'text-amber-200 hover:bg-amber-400/20': toast.type === 'warning',
                            'text-rose-200 hover:bg-rose-400/20': toast.type === 'error',
                            'text-purple-200 hover:bg-purple-400/20': toast.type === 'info'
                        }"
                        x-on:click="$store.toasts.hide(toast.id)"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
