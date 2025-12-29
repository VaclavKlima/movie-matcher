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
    $baseCancelClass = 'rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-300';
    $baseConfirmClass = 'rounded-xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:bg-rose-400';
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
            class="fixed inset-0 z-40 flex items-center justify-center px-6"
            x-show="open"
            x-cloak
            x-on:keydown.escape.window="open = false"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="absolute inset-0 bg-black/40 transition-opacity duration-300 ease-out"
                x-on:click="open = false"
                x-transition.opacity
            ></div>
            <div
                class="relative z-10 w-full max-w-md rounded-3xl border border-stone-200/80 bg-white/95 p-6 text-center shadow-[0_35px_80px_-50px_rgba(15,23,42,0.55)] transition-all duration-300 ease-out"
                x-transition
            >
                <div class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500">Confirm</div>
                <h2 class="mt-3 text-2xl font-semibold text-stone-900">{{ $title }}</h2>
                @if ($message !== '')
                    <p class="mt-2 text-sm text-stone-600">{{ $message }}</p>
                @endif
                <div class="mt-6 flex items-center justify-center gap-3">
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
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
