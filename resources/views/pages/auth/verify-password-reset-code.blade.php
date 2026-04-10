<x-layouts::auth :title="__('Enter reset code')">
    <div class="mt-4 flex flex-col gap-6">
        <x-auth-header :title="__('Check your email')" :description="__('Enter the 6-digit code we sent to your email address.')" />

        @if (session('status'))
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {{ session('status') }}
            </flux:text>
        @endif

        <form method="POST" action="{{ route('password.code.verify.store') }}" class="flex flex-col gap-4">
            @csrf
            <flux:field>
                <flux:label>{{ __('Reset Code') }}</flux:label>
                <flux:input name="code" type="text" inputmode="numeric" maxlength="6" placeholder="000000" required autofocus />
                <flux:error name="code" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Verify Code') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('Didn\'t receive a code?') }}</span>
            <flux:link :href="route('password.request')" wire:navigate>{{ __('Try again') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
