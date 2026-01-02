<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($title = $title ?? config('app.name'))
        @include('partials.head')
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="relative min-h-screen bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900 font-['Space_Grotesk'] text-slate-100 antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-32 top-24 h-64 w-64 animate-film-reel rounded-full border-8 border-amber-400/20 opacity-25"></div>
                <div class="absolute -left-40 bottom-24 h-80 w-80 animate-film-reel rounded-full border-8 border-purple-400/20 opacity-15" style="animation-delay: -8s;"></div>
                <div class="absolute inset-0 overflow-hidden">
                    <div class="animate-spotlight absolute inset-y-0 w-1/3 bg-gradient-to-r from-transparent via-amber-300/10 to-transparent"></div>
                    <div class="animate-spotlight absolute inset-y-0 left-1/2 w-1/3 bg-gradient-to-r from-transparent via-purple-300/10 to-transparent" style="animation-delay: 1.2s;"></div>
                </div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(139,92,246,0.18),transparent_50%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_75%_70%,rgba(251,191,36,0.12),transparent_50%)]"></div>
                <div class="absolute inset-0">
                    <div class="animate-marquee-lights absolute left-[12%] top-[18%] h-2 w-2 rounded-full bg-amber-300"></div>
                    <div class="animate-marquee-lights absolute left-[82%] top-[28%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 0.4s;"></div>
                    <div class="animate-marquee-lights absolute left-[68%] top-[72%] h-2 w-2 rounded-full bg-amber-300" style="animation-delay: 0.8s;"></div>
                    <div class="animate-marquee-lights absolute left-[22%] top-[76%] h-2 w-2 rounded-full bg-purple-300" style="animation-delay: 1.2s;"></div>
                </div>
            </div>

            <main class="relative z-10 mx-auto flex min-h-screen w-full max-w-6xl flex-col justify-center px-6 py-14 lg:px-10">
                @yield('content')
            </main>
        </div>
    </body>
</html>
