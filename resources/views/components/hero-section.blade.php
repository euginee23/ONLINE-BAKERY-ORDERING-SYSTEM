@use('App\Models\Setting')

@php
    $bakeryName = Setting::get('bakery_name', 'ONLINE BAKERY ORDERING SYSTEM');
@endphp

<section class="relative pt-32 pb-20 overflow-hidden sm:pt-40 sm:pb-28 lg:pb-32">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 pointer-events-none -z-10">
        <div class="absolute rounded-full -top-24 -right-24 size-96 bg-gold-200/40 blur-3xl dark:bg-gold-900/20"></div>
        <div class="absolute rounded-full -bottom-32 -left-32 size-96 bg-zinc-200/50 blur-3xl dark:bg-zinc-800/30"></div>
        <div class="absolute rounded-full top-1/2 left-1/3 size-64 bg-gold-100/30 blur-3xl dark:bg-gold-950/10"></div>
    </div>

    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            {{-- Left Content --}}
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 text-sm font-medium border rounded-full bg-gold-50 text-gold-700 border-gold-200 dark:bg-gold-950/50 dark:text-gold-400 dark:border-gold-800">
                    <span class="text-base">🍞</span>
                    <span>Fresh from the Oven</span>
                </div>

                {{-- Headline --}}
                <h1 class="text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl xl:text-7xl text-zinc-900 dark:text-white">
                    Order Your Favorite
                    <span class="text-transparent bg-gradient-to-r from-gold-600 via-gold-500 to-gold-700 bg-clip-text dark:from-gold-400 dark:via-gold-300 dark:to-gold-500">
                        Baked Goods
                    </span>
                    Online
                </h1>

                {{-- Description --}}
                <p class="max-w-xl mx-auto mt-6 text-lg leading-relaxed lg:mx-0 text-zinc-600 dark:text-zinc-400">
                    Browse our selection of freshly baked bread, cakes, pastries, and cookies. Place your order online and choose between delivery or in-store pickup.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col items-center gap-4 mt-8 sm:flex-row lg:justify-start">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full gap-2 px-8 py-3 text-base font-semibold text-white transition rounded-xl sm:w-auto bg-gold-700 hover:bg-gold-800 hover:shadow-lg hover:shadow-gold-700/25 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            Order Now
                        </a>
                    @else
                        <button
                            x-data
                            x-on:click="$dispatch('open-register-modal')"
                            class="inline-flex items-center justify-center w-full gap-2 px-8 py-3 text-base font-semibold text-white transition cursor-pointer rounded-xl sm:w-auto bg-gold-700 hover:bg-gold-800 hover:shadow-lg hover:shadow-gold-700/25 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            Get Started
                        </button>
                    @endauth
                    <a href="#menu" class="inline-flex items-center justify-center w-full gap-2 px-8 py-3 text-base font-semibold transition border rounded-xl sm:w-auto text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-800">
                        See Our Menu
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>

                {{-- Trust Badges --}}
                <div class="flex items-center justify-center gap-6 mt-8 lg:justify-start">
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-green-500 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Fresh Daily
                    </div>
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-green-500 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Free Delivery
                    </div>
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-green-500 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Easy Pickup
                    </div>
                </div>
            </div>

            {{-- Right Illustration --}}
            <div class="relative hidden lg:block">
                {{-- Main Card --}}
                <div class="relative p-6 border shadow-2xl bg-white/90 rounded-2xl border-zinc-200 dark:bg-zinc-800/90 dark:border-zinc-700 shadow-zinc-200/50 dark:shadow-zinc-950/50">
                    {{-- Browser Dots --}}
                    <div class="flex items-center gap-2 mb-4">
                        <div class="rounded-full size-3 bg-red-400/80"></div>
                        <div class="rounded-full size-3 bg-yellow-400/80"></div>
                        <div class="rounded-full size-3 bg-green-400/80"></div>
                        <div class="flex-1 h-6 ml-3 rounded-lg bg-zinc-100 dark:bg-zinc-700"></div>
                    </div>

                    {{-- Product Grid Preview --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-4 text-center transition border rounded-xl bg-zinc-50 dark:bg-zinc-700/50 border-zinc-100 dark:border-zinc-600 hover:shadow-md">
                            <div class="mb-2 text-3xl">🍞</div>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Artisan Bread</p>
                            <p class="text-xs text-gold-600 dark:text-gold-400">₱85.00</p>
                        </div>
                        <div class="p-4 text-center transition border rounded-xl bg-zinc-50 dark:bg-zinc-700/50 border-zinc-100 dark:border-zinc-600 hover:shadow-md">
                            <div class="mb-2 text-3xl">🎂</div>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Birthday Cake</p>
                            <p class="text-xs text-gold-600 dark:text-gold-400">₱550.00</p>
                        </div>
                        <div class="p-4 text-center transition border rounded-xl bg-zinc-50 dark:bg-zinc-700/50 border-zinc-100 dark:border-zinc-600 hover:shadow-md">
                            <div class="mb-2 text-3xl">🥐</div>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Croissant</p>
                            <p class="text-xs text-gold-600 dark:text-gold-400">₱65.00</p>
                        </div>
                        <div class="p-4 text-center transition border rounded-xl bg-zinc-50 dark:bg-zinc-700/50 border-zinc-100 dark:border-zinc-600 hover:shadow-md">
                            <div class="mb-2 text-3xl">🍪</div>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Cookies</p>
                            <p class="text-xs text-gold-600 dark:text-gold-400">₱120.00</p>
                        </div>
                    </div>

                    {{-- Cart Summary --}}
                    <div class="flex items-center justify-between p-3 mt-3 border rounded-lg bg-gold-50 dark:bg-gold-950/50 border-gold-200 dark:border-gold-800">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gold-600 dark:text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span class="text-sm font-medium text-gold-700 dark:text-gold-400">3 items in cart</span>
                        </div>
                        <span class="text-sm font-bold text-gold-800 dark:text-gold-300">₱700.00</span>
                    </div>
                </div>

                {{-- Floating Card: Order Confirmed --}}
                <div class="absolute p-3 border shadow-lg -top-4 -left-8 bg-white/95 dark:bg-zinc-800/95 rounded-xl border-zinc-200 dark:border-zinc-700 animate-bounce" style="animation-duration: 3s">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center bg-green-100 rounded-lg dark:bg-green-900/50 size-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-green-600 dark:text-green-400 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Order Confirmed!</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Ready in 30 minutes</p>
                        </div>
                    </div>
                </div>

                {{-- Floating Card: New Customer --}}
                <div class="absolute p-3 border shadow-lg -bottom-4 -right-6 bg-white/95 dark:bg-zinc-800/95 rounded-xl border-zinc-200 dark:border-zinc-700 animate-bounce" style="animation-duration: 4s">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center rounded-lg bg-gold-100 dark:bg-gold-900/50 size-10">
                            <span class="text-lg">⭐</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Fresh batch ready!</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Sourdough & Pastries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
