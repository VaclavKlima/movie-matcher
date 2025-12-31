@props([
    'triggerText' => 'Confirm',
    'triggerClass' => '',
    'title' => 'Are you sure?',
    'message' => '',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmAction' => '',
    'confirmClass' => '',
    'cancelClass' => '',
])

@php
    $baseTriggerClass = 'inline-flex items-center justify-center';
    $baseCancelClass = 'inline-flex items-center gap-2 rounded-xl border border-slate-600/50 bg-slate-800/50 px-5 py-3 text-sm font-bold text-slate-300 transition hover:border-slate-500 hover:bg-slate-700/50';
    $baseConfirmClass = 'group/btn relative inline-flex items-center gap-2 overflow-hidden rounded-xl border-2 border-rose-400/50 bg-gradient-to-r from-rose-500/30 to-rose-600/30 px-5 py-3 text-sm font-bold text-rose-100 shadow-lg shadow-rose-500/30 transition-all duration-300 hover:scale-105 hover:border-rose-400 hover:from-rose-500/40 hover:to-rose-600/40 active:scale-95';
@endphp

<div x-data="{ open: false }">
    <button
        type="button"
        class="{{ trim($baseTriggerClass.' '.$triggerClass) }}"
        x-on:click="open = true"
    >
        {{ $triggerText }}
    </button>

    <template x-teleport="body">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center px-6"
            x-show="open"
            x-cloak
            x-on:keydown.escape.window="open = false"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity duration-300"
                x-on:click="open = false"
                x-transition.opacity
            ></div>

            {{-- Modal Content --}}
            <div
                class="relative z-10 w-full max-w-md rounded-3xl border-2 border-rose-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 p-8 text-center shadow-2xl shadow-rose-500/30 backdrop-blur-xl transition-all duration-300"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            >
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-rose-400/50 bg-rose-500/20 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-rose-300">
                    <span class="animate-pulse">⚠️</span>
                    <span>Confirm</span>
                </div>

                {{-- Title --}}
                <h2 class="mt-6 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-200 via-rose-100 to-rose-200 drop-shadow-[0_0_20px_rgba(244,63,94,0.3)]">
                    {{ $title }}
                </h2>

                {{-- Message --}}
                @if ($message !== '')
                    <p class="mt-4 text-base text-purple-200/90">{{ $message }}</p>
                @endif

                {{-- Buttons --}}
                <div class="mt-8 flex items-center justify-center gap-3">
                    <button
                        type="button"
                        class="{{ $cancelClass !== '' ? $cancelClass : $baseCancelClass }}"
                        x-on:click="open = false"
                    >
                        {{ $cancelText }}
                    </button>

                    <button
                        type="button"
                        class="{{ $confirmClass !== '' ? $confirmClass : $baseConfirmClass }}"
                        wire:click="{{ $confirmAction }}"
                        x-on:click="open = false"
                    >
                        <span class="relative z-10 flex items-center gap-2">
                            {{ $confirmText }}
                        </span>
                        @if ($confirmClass === '')
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover/btn:translate-x-full"></div>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
