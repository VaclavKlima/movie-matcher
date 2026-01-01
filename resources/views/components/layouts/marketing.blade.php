<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="relative min-h-screen bg-stone-50 font-['Space_Grotesk'] text-stone-900 antialiased">
        {{ $slot }}

        <footer class="absolute inset-x-0 bottom-4 px-6 text-center text-xs text-amber-200/70">
            <span class="mx-auto inline-flex items-center gap-2 rounded-full bg-slate-950/40 px-3 py-1.5 text-purple-200/80 shadow-sm shadow-amber-500/10 backdrop-blur">
                <span class="font-semibold text-amber-200/80">{{ config('app.name') }}</span>
                <span class="opacity-70">•</span>
                <span>Version {{ config('version.app') }}</span>
                @if (auth()->user()?->is_admin)
                    <span class="opacity-70">•</span>
                    <a
                        href="{{ route('dashboard') }}"
                        class="pointer-events-auto font-semibold text-amber-200/90 transition hover:text-amber-100"
                    >
                        👑 Admin Dashboard
                    </a>
                @endif
            </span>
        </footer>
        <x-toast-stack />
        @fluxScripts
    </body>
</html>
