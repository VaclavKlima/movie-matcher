<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="min-h-screen bg-stone-50 font-['Space_Grotesk'] text-stone-900 antialiased">
        {{ $slot }}
        <x-toast-stack />
        @fluxScripts
    </body>
</html>
