<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="relative min-h-screen bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-900 text-slate-100">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute inset-0 opacity-70"
                style="background-image: radial-gradient(circle at 20% 20%, rgba(251, 191, 36, 0.35) 0.5px, transparent 1px), radial-gradient(circle at 80% 30%, rgba(248, 250, 252, 0.3) 0.5px, transparent 1px), radial-gradient(circle at 35% 75%, rgba(129, 140, 248, 0.25) 0.5px, transparent 1px); background-size: 140px 140px, 200px 200px, 240px 240px;"
            ></div>
            <div class="absolute -left-1/3 top-10 h-72 w-[80%] rotate-[-8deg] bg-gradient-to-r from-amber-400/20 via-amber-200/10 to-transparent blur-3xl animate-spotlight"></div>
            <div class="absolute -right-1/3 top-28 h-80 w-[80%] rotate-[8deg] bg-gradient-to-l from-purple-400/20 via-purple-300/10 to-transparent blur-3xl animate-spotlight" style="animation-delay: 1.6s"></div>
            <div class="absolute bottom-16 left-12 h-40 w-40 rounded-full bg-amber-400/15 blur-2xl animate-float"></div>
            <div class="absolute bottom-24 right-20 h-44 w-44 rounded-full bg-emerald-400/10 blur-2xl animate-float" style="animation-delay: 1.2s"></div>
            <div class="absolute left-1/4 top-1/2 h-56 w-56 rounded-full bg-purple-400/10 blur-3xl animate-drift-slow"></div>
            <div class="absolute right-1/3 top-1/4 h-36 w-36 rounded-full bg-amber-300/10 blur-2xl animate-drift-slow" style="animation-delay: 2s"></div>
        </div>
        <flux:sidebar sticky stashable class="border-e border-amber-400/30 bg-gradient-to-b from-slate-900/95 to-slate-950/95 text-slate-200 shadow-2xl shadow-amber-500/20 backdrop-blur-xl">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid text-amber-100">
                    <flux:navlist.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                        🎭 Frontend
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        🎬 Dashboard
                    </flux:navlist.item>
                    @if (auth()->user()?->is_admin)
                        <flux:navlist.item :href="url(config('pulse.path', 'pulse'))" :current="request()->is(config('pulse.path', 'pulse'))">
                            📊 Pulse
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('admin.trends')" :current="request()->routeIs('admin.trends')" wire:navigate>
                            📈 Trends Dashboard
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('admin.rooms')" :current="request()->routeIs('admin.rooms')" wire:navigate>
                            🎫 Screening Rooms
                        </flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                🗂️ {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item href="https://laravel.com/docs/starter-kits" target="_blank">
                📚 {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" wire:navigate>⚙️ {{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" class="w-full">
                            🚪 {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <footer class="border-t border-amber-400/20 bg-gradient-to-r from-slate-900/95 to-slate-950/95 px-6 py-4 text-xs text-amber-100/70">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                <span>{{ config('app.name') }}</span>
                <span>Version {{ config('version.app') }}</span>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
