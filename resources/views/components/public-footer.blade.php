@use('App\Models\Setting')

@php
    $bakeryName = Setting::get('bakery_name', 'ONLINE BAKERY ORDERING SYSTEM');
    $bakeryAddress = Setting::get('bakery_address', '123 Baker Street, Manila, Philippines');
    $bakeryPhone = Setting::get('bakery_phone', '+63 912 345 6789');
    $bakeryEmail = Setting::get('bakery_email', 'info@bakerysystem.com');
@endphp

<footer id="contact" class="border-t bg-zinc-950 border-zinc-800">
    <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            {{-- Brand --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center rounded-lg size-9 bg-gold-600">
                        <x-app-logo-icon class="text-white size-5 fill-current" />
                    </div>
                    <span class="text-lg font-bold text-white">{{ $bakeryName }}</span>
                </div>
                <p class="text-sm leading-relaxed text-zinc-400">
                    Your favorite bakery, now online. Fresh bread, cakes, pastries, and more — delivered to your doorstep or ready for pickup.
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold tracking-wider text-white uppercase">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="#menu" class="text-sm transition text-zinc-400 hover:text-gold-400">Our Menu</a></li>
                    <li><a href="#how-it-works" class="text-sm transition text-zinc-400 hover:text-gold-400">How It Works</a></li>
                    <li><a href="#about" class="text-sm transition text-zinc-400 hover:text-gold-400">About Us</a></li>
                    <li><a href="#contact" class="text-sm transition text-zinc-400 hover:text-gold-400">Contact</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold tracking-wider text-white uppercase">Contact Us</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-4 shrink-0 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm text-zinc-400">{{ $bakeryAddress }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm text-zinc-400">{{ $bakeryPhone }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm text-zinc-400">{{ $bakeryEmail }}</span>
                    </li>
                </ul>
            </div>

            {{-- Operating Hours --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold tracking-wider text-white uppercase">Operating Hours</h3>
                <ul class="space-y-3">
                    <li class="flex justify-between text-sm">
                        <span class="text-zinc-400">Monday - Friday</span>
                        <span class="font-medium text-zinc-300">6:00 AM - 8:00 PM</span>
                    </li>
                    <li class="flex justify-between text-sm">
                        <span class="text-zinc-400">Saturday</span>
                        <span class="font-medium text-zinc-300">7:00 AM - 9:00 PM</span>
                    </li>
                    <li class="flex justify-between text-sm">
                        <span class="text-zinc-400">Sunday</span>
                        <span class="font-medium text-zinc-300">7:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="pt-8 mt-12 border-t border-zinc-800">
            <p class="text-sm text-center text-zinc-500">
                &copy; {{ date('Y') }} {{ $bakeryName }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
