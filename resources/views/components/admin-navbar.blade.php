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
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
                <div class="flex items-center justify-center rounded-lg size-9 bg-gold-700 dark:bg-gold-500">
                    <x-app-logo-icon class="text-white size-5 fill-current dark:text-zinc-900" />
                </div>
                <span class="hidden sm:block text-lg font-bold tracking-tight text-zinc-900 dark:text-white truncate max-w-[160px] lg:max-w-none">{{ $bakeryName }}</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="items-center hidden gap-1 md:flex">
                <a
                    href="{{ route('admin.dashboard') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.dashboard'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.dashboard'),
                    ])
                >
                    Dashboard
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.categories.index'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.categories.index'),
                    ])
                >
                    Categories
                </a>
                <a
                    href="{{ route('admin.products.index') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.products.index'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.products.index'),
                    ])
                >
                    Products
                </a>
                <a
                    href="{{ route('admin.orders.index') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.orders.index'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.orders.index'),
                    ])
                >
                    Orders
                </a>
                <a
                    href="{{ route('admin.reports.index') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.reports.index'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.reports.index'),
                    ])
                >
                    Reports
                </a>
                <a
                    href="{{ route('admin.business-settings') }}"
                    wire:navigate
                    @class([
                        'px-3 py-2 text-sm font-medium transition rounded-lg',
                        'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.business-settings'),
                        'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.business-settings'),
                    ])
                >
                    Settings
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
                        <svg x-cloak x-show="$flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark)" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-cloak x-show="!($flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark))" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

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

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ userOpen: false }" x-on:click.away="userOpen = false">
                    <button
                        x-on:click="userOpen = !userOpen"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium transition rounded-lg text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        <span class="flex items-center justify-center rounded-lg size-7 bg-gold-100 text-gold-700 text-xs font-bold dark:bg-gold-900/30 dark:text-gold-400">
                            {{ auth()->user()->initials() }}
                        </span>
                        <span>{{ auth()->user()->name }}</span>
                        <svg class="size-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>

                    <div
                        x-cloak
                        x-show="userOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute right-0 z-50 w-48 py-1 mt-2 border shadow-xl bg-white rounded-xl border-zinc-200 shadow-zinc-900/10 dark:bg-zinc-900 dark:border-zinc-700"
                    >
                        <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <p class="text-xs font-semibold text-zinc-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a
                            href="{{ route('profile.edit') }}"
                            wire:navigate
                            class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            x-on:click="userOpen = false"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button
                                type="submit"
                                class="flex items-center w-full gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
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
                <a href="{{ route('admin.dashboard') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.dashboard'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.dashboard')])>Dashboard</a>
                <a href="{{ route('admin.categories.index') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.categories.index'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.categories.index')])>Categories</a>
                <a href="{{ route('admin.products.index') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.products.index'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.products.index')])>Products</a>
                <a href="{{ route('admin.orders.index') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.orders.index'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.orders.index')])>Orders</a>
                <a href="{{ route('admin.reports.index') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.reports.index'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.reports.index')])>Reports</a>
                <a href="{{ route('admin.business-settings') }}" wire:navigate x-on:click="mobileOpen = false" @class(['px-3 py-2 text-sm font-medium transition rounded-lg', 'text-gold-700 bg-gold-50 dark:text-gold-400 dark:bg-gold-900/20' => request()->routeIs('admin.business-settings'), 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800' => ! request()->routeIs('admin.business-settings')])>Settings</a>
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
                <div class="flex items-center gap-2 px-3 py-2">
                    <span class="flex items-center justify-center rounded-lg size-8 bg-gold-100 text-gold-700 text-xs font-bold dark:bg-gold-900/30 dark:text-gold-400">
                        {{ auth()->user()->initials() }}
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" wire:navigate x-on:click="mobileOpen = false" class="px-4 py-2 text-sm font-medium text-center transition border rounded-lg text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-800">
                    Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-center text-white transition rounded-lg bg-red-600 hover:bg-red-700">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
