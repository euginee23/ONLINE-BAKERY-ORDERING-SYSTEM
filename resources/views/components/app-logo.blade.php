@use('App\Models\Setting')

@props([
    'sidebar' => false,
])

@php
    $bakeryName = Setting::get('bakery_name', 'ONLINE BAKERY ORDERING SYSTEM');
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$bakeryName" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-gold-700 dark:bg-gold-500">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-zinc-900" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$bakeryName" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-gold-700 dark:bg-gold-500">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-zinc-900" />
        </x-slot>
    </flux:brand>
@endif
