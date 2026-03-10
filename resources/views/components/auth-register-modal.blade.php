<div
    x-data="{ open: false }"
    x-on:open-register-modal.window="open = true"
    x-on:close-register-modal.window="open = false"
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
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Create an account</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Sign up to start ordering your favorite baked goods</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="modal-register-name" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Full name</label>
                    <input
                        id="modal-register-name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Full name"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modal-register-email" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Email address</label>
                    <input
                        id="modal-register-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modal-register-password" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                    <input
                        id="modal-register-password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Password"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modal-register-password-confirm" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Confirm password</label>
                    <input
                        id="modal-register-password-confirm"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                </div>

                <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400">
                    Create account
                </button>
            </form>

            {{-- Switch to Login --}}
            <p class="mt-6 text-sm text-center text-zinc-600 dark:text-zinc-400">
                Already have an account?
                <button x-on:click="open = false; $dispatch('open-login-modal')" class="font-medium transition text-gold-600 hover:text-gold-700 dark:text-gold-400 dark:hover:text-gold-300">
                    Log in
                </button>
            </p>
        </div>
    </div>
</div>
