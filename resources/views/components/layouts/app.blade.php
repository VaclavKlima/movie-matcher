<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <x-toast-stack />
</x-layouts.app.sidebar>
