@props([
    'icon' => '',
    'title' => 'Feature',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'group rounded-2xl border border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700 dark:bg-zinc-800']) }}>
    {{-- Icon Circle --}}
    <div class="flex items-center justify-center mb-4 border rounded-xl size-12 bg-gold-50 border-gold-200 dark:bg-gold-950/50 dark:border-gold-800">
        @if ($icon)
            <span class="text-xl">{{ $icon }}</span>
        @else
            {{ $slot }}
        @endif
    </div>

    <h3 class="mb-2 text-base font-bold text-zinc-900 dark:text-white">{{ $title }}</h3>
    <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
</div>
