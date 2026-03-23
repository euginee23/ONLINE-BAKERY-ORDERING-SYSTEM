<x-layouts::app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <flux:heading size="xl">{{ __('Admin Dashboard') }}</flux:heading>
        <flux:text class="text-zinc-500">{{ __('Welcome back! Here\'s an overview of your bakery operations.') }}</flux:text>

        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            {{-- Placeholder stat cards — will be populated in Phase 7 --}}
            <div class="flex flex-col gap-1 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text class="text-sm text-zinc-500">{{ __('Total Orders') }}</flux:text>
                <flux:heading size="xl">0</flux:heading>
            </div>
            <div class="flex flex-col gap-1 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text class="text-sm text-zinc-500">{{ __('Pending Orders') }}</flux:text>
                <flux:heading size="xl">0</flux:heading>
            </div>
            <div class="flex flex-col gap-1 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text class="text-sm text-zinc-500">{{ __('Products') }}</flux:text>
                <flux:heading size="xl">0</flux:heading>
            </div>
            <div class="flex flex-col gap-1 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text class="text-sm text-zinc-500">{{ __('Categories') }}</flux:text>
                <flux:heading size="xl">0</flux:heading>
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
            <flux:text class="text-zinc-500">{{ __('Recent orders and detailed analytics will appear here once orders start coming in.') }}</flux:text>
        </div>
    </div>
</x-layouts::app>
