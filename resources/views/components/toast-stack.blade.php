<div
    wire:ignore
    x-data
    x-on:toast.window="$store.toasts.push($event.detail.message, $event.detail.type || 'info')"
    class="pointer-events-none fixed inset-x-0 top-6 z-50 flex justify-center px-4"
>
    <div class="flex w-full max-w-md flex-col gap-3">
        <template x-for="toast in $store.toasts.list" :key="toast.id">
            <div
                class="pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3 text-sm font-semibold shadow-[0_20px_45px_-25px_rgba(15,23,42,0.55)] transition-all duration-300 ease-out"
                :class="{
                    // Type-based styling
                    'border-emerald-200 bg-emerald-50/90 text-emerald-700': toast.type === 'success',
                    'border-amber-200 bg-amber-50/90 text-amber-700': toast.type === 'warning',
                    'border-rose-200 bg-rose-50/90 text-rose-700': toast.type === 'error',
                    'border-stone-200/80 bg-white/95 text-stone-800': toast.type === 'info',
                    // Animation states
                    'translate-y-0 scale-100 opacity-100 rotate-0': toast.state === 'visible',
                    '-translate-y-4 scale-95 opacity-0 rotate-1': toast.state === 'entering',
                    '-translate-y-2 scale-95 opacity-0 -rotate-1': toast.state === 'leaving'
                }"
            >
                <div
                    class="absolute inset-0 transition-opacity duration-300"
                    :class="{
                        'bg-gradient-to-r from-emerald-200/40 via-transparent to-emerald-100/40 opacity-100': toast.type === 'success',
                        'bg-gradient-to-r from-amber-200/40 via-transparent to-amber-100/40 opacity-100': toast.type === 'warning',
                        'bg-gradient-to-r from-rose-200/40 via-transparent to-rose-100/40 opacity-100': toast.type === 'error',
                        'opacity-0': toast.type === 'info'
                    }"
                ></div>
                <div
                    class="absolute left-0 top-0 h-full w-1.5"
                    :class="{
                        'bg-emerald-500/70': toast.type === 'success',
                        'bg-amber-500/70': toast.type === 'warning',
                        'bg-rose-500/70': toast.type === 'error',
                        'bg-stone-900/10': toast.type === 'info'
                    }"
                ></div>
                <div class="relative flex items-center justify-between gap-3">
                    <span x-text="toast.message"></span>
                    <button
                        type="button"
                        class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400 transition-colors hover:text-stone-600"
                        x-on:click="$store.toasts.hide(toast.id)"
                    >
                        Close
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
