<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="relative flex min-h-screen flex-col bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900 font-['Space_Grotesk'] text-slate-100 antialiased">
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <div class="absolute -right-32 top-20 h-64 w-64 animate-film-reel rounded-full border-8 border-amber-400/20 opacity-20"></div>
            <div class="absolute -left-32 bottom-40 h-96 w-96 animate-film-reel rounded-full border-8 border-purple-400/20 opacity-10" style="animation-delay: -10s;"></div>

            <div class="absolute inset-0 overflow-hidden">
                <div class="animate-spotlight absolute inset-y-0 w-1/3 bg-gradient-to-r from-transparent via-amber-300/10 to-transparent"></div>
            </div>

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(139,92,246,0.15),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(251,191,36,0.1),transparent_50%)]"></div>

            <div class="absolute inset-0">
                <div class="animate-marquee-lights absolute left-[10%] top-[15%] h-2 w-2 rounded-full bg-amber-300"></div>
                <div class="animate-marquee-lights absolute left-[85%] top-[25%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 0.5s;"></div>
                <div class="animate-marquee-lights absolute left-[60%] top-[70%] h-2 w-2 rounded-full bg-amber-300" style="animation-delay: 1s;"></div>
                <div class="animate-marquee-lights absolute left-[20%] top-[80%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 1.5s;"></div>
            </div>
        </div>

        <div class="relative z-10 flex-1">
            {{ $slot }}
        </div>

    <footer class="relative z-10 mt-auto px-4 pb-6 pt-8 text-center text-xs text-amber-200/70 sm:px-6">
        <span class="mx-auto inline-flex items-center gap-2 rounded-full bg-slate-950/40 px-3 py-1.5 text-purple-200/80 shadow-sm shadow-amber-500/10 backdrop-blur">
            <span class="font-semibold text-amber-200/80">{{ config('app.name') }}</span>
            <span class="opacity-70">•</span>
            <span>Version {{ config('version.app') }}</span>
                <span class="opacity-70">•</span>
                <a
                    href="{{ route('dashboard') }}"
                    class="pointer-events-auto font-semibold text-amber-200/90 transition hover:text-amber-100"
                >
                    👑 Admin Dashboard
                </a>
        </span>
        <div class="mt-3 flex flex-wrap items-center justify-center gap-3 text-[0.65rem] text-purple-200/80">
            <a
                href="https://www.themoviedb.org"
                class="pointer-events-auto inline-flex items-center rounded-full border border-amber-400/30 bg-slate-950/40 px-3 py-1 shadow-sm shadow-amber-500/10 transition hover:border-amber-400/60"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="The Movie Database"
            >
                <img
                    src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_long_2-9665a76b1ae401a510ec1e0ca40ddcb3b0cfe45f1d51b77a308fea0845885648.svg"
                    alt="The Movie Database"
                    class="h-5 w-auto"
                    loading="lazy"
                />
            </a>
            <span class="max-w-md text-center">
                This product uses the TMDB API but is not endorsed or certified by TMDB.
            </span>
        </div>
    </footer>
    <x-toast-stack />
    @fluxScripts
    </body>
</html>
