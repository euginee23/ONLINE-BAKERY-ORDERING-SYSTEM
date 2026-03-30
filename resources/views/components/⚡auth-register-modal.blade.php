<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[On('open-register-modal')]
    public function open(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
    }

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::default()],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => UserRole::Customer,
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('profile.edit'), navigate: true);
    }
};
?>

<div
    x-data="{ open: @entangle('showModal') }"
    x-on:keydown.escape.window="if (open) { open = false; $wire.close() }"
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
        x-on:click="open = false; $wire.close()"
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
                <button wire:click="close" class="transition text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
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
            <form wire:submit="register" class="space-y-4">
                <div>
                    <label for="reg-name" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Full name</label>
                    <input
                        id="reg-name"
                        wire:model="name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Full name"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400 @error('name') border-red-500 dark:border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reg-email" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Email address</label>
                    <input
                        id="reg-email"
                        wire:model="email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reg-password" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                    <input
                        id="reg-password"
                        wire:model="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Password"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400 @error('password') border-red-500 dark:border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reg-password-confirm" class="block mb-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300">Confirm password</label>
                    <input
                        id="reg-password-confirm"
                        wire:model="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-zinc-900 border-zinc-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 dark:focus:ring-gold-400"
                    >
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full px-4 py-2.5 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="register">Create account</span>
                    <span wire:loading wire:target="register">Creating account...</span>
                </button>
            </form>

            {{-- Switch to Login --}}
            <p class="mt-6 text-sm text-center text-zinc-600 dark:text-zinc-400">
                Already have an account?
                <button
                    wire:click="close"
                    x-on:click="$dispatch('open-login-modal')"
                    class="font-medium transition text-gold-600 hover:text-gold-700 dark:text-gold-400 dark:hover:text-gold-300"
                >
                    Log in
                </button>
            </p>
        </div>
    </div>
</div>
