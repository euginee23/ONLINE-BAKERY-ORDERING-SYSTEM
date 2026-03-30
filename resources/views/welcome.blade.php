@use('App\Models\Setting')

@php
    $bakeryName = Setting::get('bakery_name', 'ONLINE BAKERY ORDERING SYSTEM');
    $businessName = Setting::get('business_name', $bakeryName);
    $businessOwner = Setting::get('business_owner');
    $contactNumber = Setting::get('contact_number');
    $businessEmail = Setting::get('business_email');
    $businessAddress = Setting::get('business_address');
    $businessDescription = Setting::get('business_description');
    $businessHours = Setting::get('business_hours');
    $hasBusinessInfo = $businessOwner || $contactNumber || $businessAddress;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => $businessName])
    </head>
    <body class="min-h-screen antialiased bg-white text-zinc-900 dark:bg-zinc-950 dark:text-white">

        {{-- Navbar --}}
        <x-public-navbar />

        {{-- Hero Section --}}
        <x-hero-section />

        {{-- ==================== Menu Section ==================== --}}
        <section id="menu" class="py-20 bg-zinc-50 dark:bg-zinc-900/50 sm:py-28">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="max-w-2xl mx-auto mb-12 text-center sm:mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 text-sm font-medium border rounded-full bg-gold-50 text-gold-700 border-gold-200 dark:bg-gold-950/50 dark:text-gold-400 dark:border-gold-800">
                        <span class="text-base">🧁</span>
                        <span>Our Products</span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl text-zinc-900 dark:text-white">
                        Browse Our <span class="text-transparent bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">Menu</span>
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Explore our selection of freshly baked goods made with the finest ingredients.
                    </p>
                </div>

                {{-- Product Showcase (lazy-loaded Livewire component) --}}
                <livewire:public-product-showcase lazy />
            </div>
        </section>

        {{-- ==================== How It Works Section ==================== --}}
        <section id="how-it-works" class="py-20 sm:py-28">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="max-w-2xl mx-auto mb-12 text-center sm:mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 text-sm font-medium border rounded-full bg-gold-50 text-gold-700 border-gold-200 dark:bg-gold-950/50 dark:text-gold-400 dark:border-gold-800">
                        <span class="text-base">📋</span>
                        <span>Simple Process</span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl text-zinc-900 dark:text-white">
                        How It <span class="text-transparent bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">Works</span>
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        Order your favorite baked goods in three easy steps.
                    </p>
                </div>

                {{-- Steps --}}
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-12">
                    {{-- Step 1 --}}
                    <div class="relative text-center">
                        <div class="flex items-center justify-center mx-auto mb-6 text-2xl font-black text-white rounded-2xl size-16 bg-gold-700 dark:bg-gold-500 dark:text-zinc-900">1</div>
                        {{-- Connector (hidden on mobile) --}}
                        <div class="absolute hidden -translate-y-1/2 lg:block top-8 left-[calc(50%+3rem)] w-[calc(100%-6rem)]">
                            <div class="h-0.5 w-full border-t-2 border-dashed border-gold-300 dark:border-gold-700"></div>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-zinc-900 dark:text-white">Browse Products</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            Explore our menu of freshly baked bread, cakes, pastries, and cookies with real-time availability.
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative text-center">
                        <div class="flex items-center justify-center mx-auto mb-6 text-2xl font-black text-white rounded-2xl size-16 bg-gold-700 dark:bg-gold-500 dark:text-zinc-900">2</div>
                        <div class="absolute hidden -translate-y-1/2 lg:block top-8 left-[calc(50%+3rem)] w-[calc(100%-6rem)]">
                            <div class="h-0.5 w-full border-t-2 border-dashed border-gold-300 dark:border-gold-700"></div>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-zinc-900 dark:text-white">Place Your Order</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            Add items to your cart, choose between delivery or in-store pickup, and confirm your order.
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="text-center">
                        <div class="flex items-center justify-center mx-auto mb-6 text-2xl font-black text-white rounded-2xl size-16 bg-gold-700 dark:bg-gold-500 dark:text-zinc-900">3</div>
                        <h3 class="mb-2 text-lg font-bold text-zinc-900 dark:text-white">Pickup or Delivery</h3>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            Receive an order confirmation and pick up your order at the bakery or have it delivered to your door.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== Features / About Section ==================== --}}
        <section id="about" class="py-20 bg-zinc-50 dark:bg-zinc-900/50 sm:py-28">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="max-w-2xl mx-auto mb-12 text-center sm:mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 text-sm font-medium border rounded-full bg-gold-50 text-gold-700 border-gold-200 dark:bg-gold-950/50 dark:text-gold-400 dark:border-gold-800">
                        <span class="text-base">✨</span>
                        <span>Why Choose Us</span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl text-zinc-900 dark:text-white">
                        About Our <span class="text-transparent bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">System</span>
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                        A modern online ordering system designed to make your bakery experience seamless.
                    </p>
                </div>

                {{-- Feature Cards --}}
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <x-feature-card
                        icon="🛒"
                        title="Online Ordering"
                        description="Browse and order bakery products from anywhere without visiting the store."
                    />
                    <x-feature-card
                        icon="📦"
                        title="Real-time Inventory"
                        description="Check product availability instantly with live inventory tracking and updates."
                    />
                    <x-feature-card
                        icon="✅"
                        title="Order Confirmation"
                        description="Receive instant confirmation after placing your order with estimated ready time."
                    />
                    <x-feature-card
                        icon="📊"
                        title="Admin Dashboard"
                        description="Powerful dashboard for bakery staff to manage products, orders, and inventory."
                    />
                </div>

                {{-- Stats Row --}}
                <div class="grid gap-8 mt-16 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="text-4xl font-black text-transparent sm:text-5xl bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">100%</div>
                        <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">Fresh Daily</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-transparent sm:text-5xl bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">50+</div>
                        <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">Product Varieties</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-transparent sm:text-5xl bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">1000+</div>
                        <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">Happy Customers</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-transparent sm:text-5xl bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">24/7</div>
                        <p class="mt-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">Online Ordering Available</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== Business Info Section ==================== --}}
        @if($hasBusinessInfo)
        <section id="contact" class="py-20 sm:py-28">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto mb-12 text-center sm:mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 text-sm font-medium border rounded-full bg-gold-50 text-gold-700 border-gold-200 dark:bg-gold-950/50 dark:text-gold-400 dark:border-gold-800">
                        <span class="text-base">🏪</span>
                        <span>About Us</span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl text-zinc-900 dark:text-white">
                        Meet Your <span class="text-transparent bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text dark:from-gold-400 dark:to-gold-500">Bakery</span>
                    </h2>
                    @if($businessDescription)
                        <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">{{ $businessDescription }}</p>
                    @endif
                </div>

                <div class="max-w-3xl mx-auto overflow-hidden bg-white border shadow-xl dark:bg-zinc-900 rounded-3xl border-zinc-200 dark:border-zinc-700 shadow-zinc-900/10 dark:shadow-zinc-950/50">
                    <div class="h-2 bg-gradient-to-r from-gold-500 to-gold-700"></div>
                    <div class="p-8 sm:p-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex items-center justify-center rounded-2xl size-16 bg-gold-100 dark:bg-gold-900/30 shrink-0">
                                <span class="text-3xl">🧁</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-zinc-900 dark:text-white">{{ $businessName }}</h3>
                                @if($businessOwner)
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Owned by <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $businessOwner }}</span></p>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            @if($contactNumber)
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center justify-center mt-0.5 rounded-xl size-9 bg-gold-50 dark:bg-gold-900/20 shrink-0">
                                        <svg class="text-gold-600 dark:text-gold-400 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Phone</p>
                                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $contactNumber }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($businessEmail)
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center justify-center mt-0.5 rounded-xl size-9 bg-gold-50 dark:bg-gold-900/20 shrink-0">
                                        <svg class="text-gold-600 dark:text-gold-400 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Email</p>
                                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $businessEmail }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($businessAddress)
                                <div class="flex items-start gap-3 sm:col-span-2">
                                    <div class="flex items-center justify-center mt-0.5 rounded-xl size-9 bg-gold-50 dark:bg-gold-900/20 shrink-0">
                                        <svg class="text-gold-600 dark:text-gold-400 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Store Address</p>
                                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $businessAddress }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($businessHours)
                                <div class="flex items-start gap-3 sm:col-span-2">
                                    <div class="flex items-center justify-center mt-0.5 rounded-xl size-9 bg-gold-50 dark:bg-gold-900/20 shrink-0">
                                        <svg class="text-gold-600 dark:text-gold-400 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Business Hours</p>
                                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $businessHours }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- ==================== CTA Section ==================== --}}
        <section class="relative py-20 overflow-hidden sm:py-28">
            {{-- Background --}}
            <div class="absolute inset-0 bg-gradient-to-br from-gold-700 via-gold-800 to-zinc-900 dark:from-gold-900 dark:via-zinc-900 dark:to-zinc-950"></div>
            <div class="absolute inset-0 opacity-30">
                <div class="absolute rounded-full -top-32 -right-32 size-96 bg-gold-500/30 blur-3xl"></div>
                <div class="absolute rounded-full -bottom-32 -left-32 size-96 bg-gold-400/20 blur-3xl"></div>
            </div>

            <div class="relative px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                <span class="text-5xl">🍰</span>
                <h2 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Ready to Order?
                </h2>
                <p class="max-w-2xl mx-auto mt-4 text-lg text-gold-100/80">
                    Join us today and enjoy freshly baked goods delivered right to your doorstep. Create your account and start ordering now!
                </p>
                <div class="flex flex-col items-center justify-center gap-4 mt-8 sm:flex-row">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3 text-base font-semibold transition bg-white rounded-xl text-gold-700 hover:bg-gold-50 hover:shadow-lg">
                            Go to Dashboard
                        </a>
                    @else
                        <button
                            x-data
                            x-on:click="$dispatch('open-register-modal')"
                            class="inline-flex items-center gap-2 px-8 py-3 text-base font-semibold transition bg-white cursor-pointer rounded-xl text-gold-700 hover:bg-gold-50 hover:shadow-lg"
                        >
                            Create Account
                        </button>
                        <button
                            x-data
                            x-on:click="$dispatch('open-login-modal')"
                            class="inline-flex items-center gap-2 px-8 py-3 text-base font-semibold text-white/90 transition border cursor-pointer rounded-xl border-white/30 hover:bg-white/10"
                        >
                            Log in
                        </button>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <x-public-footer />

        {{-- Auth Modals (guests only) --}}
        @guest
            <livewire:auth-login-modal />
            <livewire:auth-register-modal />
        @endguest

        {{-- Notification Toast --}}
        <x-notification-toast />

        {{-- Scroll to Top Button --}}
        <div
            x-data="{ showButton: false }"
            x-on:scroll.window="showButton = window.scrollY > 400"
        >
            <button
                x-show="showButton"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                x-cloak
                class="fixed z-50 flex items-center justify-center text-white transition border shadow-lg cursor-pointer bottom-6 right-6 size-12 rounded-xl bg-gold-700 hover:bg-gold-800 border-gold-600 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400 dark:border-gold-400"
                aria-label="Scroll to top"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                </svg>
            </button>
        </div>

        {{-- Smooth Scroll Script --}}
        <script>
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const offset = 80;
                        const position = target.getBoundingClientRect().top + window.scrollY - offset;
                        window.scrollTo({ top: position, behavior: 'smooth' });
                    }
                });
            });
        </script>

        @fluxScripts
    </body>
</html>
