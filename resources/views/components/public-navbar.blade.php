@use('App\Models\Setting')

@php
    $bakeryName = Setting::get('bakery_name', 'ONLINE BAKERY ORDERING SYSTEM');
@endphp

<header
    x-data="{ mobileOpen: false }"
    class="fixed top-0 left-0 right-0 z-50 border-b bg-white/80 backdrop-blur-lg border-zinc-200 dark:border-zinc-800 dark:bg-zinc-900/80"
>
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo & Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="flex items-center justify-center rounded-lg size-9 bg-gold-700 dark:bg-gold-500">
                    <x-app-logo-icon class="text-white size-5 fill-current dark:text-zinc-900" />
                </div>
                <span class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $bakeryName }}</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="items-center hidden gap-1 md:flex">
                <a href="#menu" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">
                    Menu
                </a>
                <a href="#how-it-works" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">
                    How It Works
                </a>
                <a href="#about" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">
                    About
                </a>
                <a href="#contact" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">
                    Contact
                </a>
            </nav>

            {{-- Desktop Actions --}}
            <div class="items-center hidden gap-3 md:flex">
                {{-- Theme Toggle Dropdown --}}
                <div class="relative" x-data="{ open: false }" x-on:click.away="open = false">
                    <button
                        x-on:click="open = !open"
                        class="flex items-center justify-center transition rounded-lg size-9 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800"
                        aria-label="Select theme"
                    >
                        {{-- Sun: shown when dark mode is active --}}
                        <svg x-cloak x-show="$flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark)" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{-- Moon: shown when light mode is active --}}
                        <svg x-cloak x-show="!($flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark))" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div
                        x-cloak
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute right-0 z-50 w-40 py-1 mt-2 border shadow-xl bg-white rounded-xl border-zinc-200 shadow-zinc-900/10 dark:bg-zinc-900 dark:border-zinc-700"
                    >
                        <button
                            x-on:click="$flux.appearance = 'light'; open = false"
                            class="flex items-center w-full gap-3 px-4 py-2 text-sm transition cursor-pointer"
                            :class="$flux.appearance === 'light' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20' : 'text-zinc-700 dark:text-zinc-300 hover:text-gold-700 dark:hover:text-gold-400 hover:bg-gold-50 dark:hover:bg-zinc-800'"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Light
                        </button>
                        <button
                            x-on:click="$flux.appearance = 'dark'; open = false"
                            class="flex items-center w-full gap-3 px-4 py-2 text-sm transition cursor-pointer"
                            :class="$flux.appearance === 'dark' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20' : 'text-zinc-700 dark:text-zinc-300 hover:text-gold-700 dark:hover:text-gold-400 hover:bg-gold-50 dark:hover:bg-zinc-800'"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            Dark
                        </button>
                        <button
                            x-on:click="$flux.appearance = 'system'; open = false"
                            class="flex items-center w-full gap-3 px-4 py-2 text-sm transition cursor-pointer"
                            :class="$flux.appearance === 'system' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20' : 'text-zinc-700 dark:text-zinc-300 hover:text-gold-700 dark:hover:text-gold-400 hover:bg-gold-50 dark:hover:bg-zinc-800'"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            System
                        </button>
                    </div>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                        Dashboard
                    </a>
                @else
                    <button
                        x-data
                        x-on:click="$dispatch('open-login-modal')"
                        class="px-4 py-2 text-sm font-semibold transition border rounded-lg text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-800"
                    >
                        Log in
                    </button>
                    <button
                        x-data
                        x-on:click="$dispatch('open-register-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
                    >
                        Sign up
                    </button>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button
                x-on:click="mobileOpen = !mobileOpen"
                class="flex items-center justify-center transition rounded-lg md:hidden size-9 text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800"
                aria-label="Toggle menu"
            >
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
            class="pb-4 border-t md:hidden border-zinc-200 dark:border-zinc-800"
        >
            <nav class="flex flex-col gap-1 pt-3">
                <a href="#menu" x-on:click="mobileOpen = false" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">Menu</a>
                <a href="#how-it-works" x-on:click="mobileOpen = false" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">How It Works</a>
                <a href="#about" x-on:click="mobileOpen = false" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">About</a>
                <a href="#contact" x-on:click="mobileOpen = false" class="px-3 py-2 text-sm font-medium transition rounded-lg text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">Contact</a>
            </nav>
            {{-- Theme Selection --}}
            <div class="pt-3 mt-3 border-t border-zinc-200 dark:border-zinc-800">
                <p class="px-1 mb-2 text-xs font-semibold tracking-wider uppercase text-zinc-400 dark:text-zinc-500">Theme</p>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        x-on:click="$flux.appearance = 'light'"
                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium transition rounded-lg border cursor-pointer"
                        :class="$flux.appearance === 'light' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20 border-gold-300 dark:border-gold-700' : 'text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Light
                    </button>
                    <button
                        x-on:click="$flux.appearance = 'dark'"
                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium transition rounded-lg border cursor-pointer"
                        :class="$flux.appearance === 'dark' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20 border-gold-300 dark:border-gold-700' : 'text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        Dark
                    </button>
                    <button
                        x-on:click="$flux.appearance = 'system'"
                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium transition rounded-lg border cursor-pointer"
                        :class="$flux.appearance === 'system' ? 'text-gold-700 dark:text-gold-400 bg-gold-50 dark:bg-gold-900/20 border-gold-300 dark:border-gold-700' : 'text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        System
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-3 mt-3 border-t border-zinc-200 dark:border-zinc-800">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold text-center text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                        Dashboard
                    </a>
                @else
                    <button x-on:click="$dispatch('open-login-modal'); mobileOpen = false" class="px-4 py-2 text-sm font-semibold transition border rounded-lg text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-800">
                        Log in
                    </button>
                    <button x-on:click="$dispatch('open-register-modal'); mobileOpen = false" class="px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                        Sign up
                    </button>
                @endauth
            </div>
        </div>
    </div>
</header>
