<div
    x-data="{ open: false }"
    x-on:open-login-modal.window="open = true"
    x-on:close-login-modal.window="open = false"
    x-on:keydown.escape.window="open = false"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
        x-on:click="open = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md p-8 border shadow-2xl bg-white rounded-2xl border-zinc-200 dark:bg-zinc-900 dark:border-zinc-700" x-on:click.stop>
            {{-- Close Button --}}
            <div class="flex justify-end mb-2">
                <button x-on:click="open = false" class="transition text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Header --}}
            <div class="mb-6 text-center">
                <div class="flex items-center justify-center mx-auto mb-4 rounded-xl size-12 bg-gold-100 dark:bg-gold-950/50">
                    <x-app-logo-icon class="size-6 fill-current text-gold-700 dark:text-gold-400" />
                </div>
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Welcome back</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Log in to your account to continue</p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            {{-- Form --}}
            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="modal-login-email" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Email address</label>
                    <input
                        id="modal-login-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="modal-login-password" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium transition text-gold-600 hover:text-gold-700 dark:text-gold-400 dark:hover:text-gold-300">Forgot password?</a>
                    </div>
                    <input
                        id="modal-login-password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input id="modal-remember" name="remember" type="checkbox" class="border rounded size-4 text-gold-600 border-zinc-300 focus:ring-gold-500 dark:border-zinc-600 dark:bg-zinc-800" {{ old('remember') ? 'checked' : '' }}>
                    <label for="modal-remember" class="text-sm text-zinc-600 dark:text-zinc-400">Remember me</label>
                </div>

                <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                    Log in
                </button>
            </form>

            {{-- Switch to Register --}}
            <p class="mt-6 text-sm text-center text-zinc-600 dark:text-zinc-400">
                Don't have an account?
                <button x-on:click="open = false; $dispatch('open-register-modal')" class="font-medium transition text-gold-600 hover:text-gold-700 dark:text-gold-400 dark:hover:text-gold-300">
                    Sign up
                </button>
            </p>
        </div>
    </div>
</div>
