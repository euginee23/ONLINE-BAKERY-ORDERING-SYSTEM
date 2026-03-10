@props([
    'icon' => '🍞',
    'title' => 'Category',
    'description' => '',
    'count' => 0,
])

<div {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-gold-300 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-gold-700']) }}>
    {{-- Gradient Accent on Hover --}}
    <div class="absolute inset-0 transition-opacity duration-300 opacity-0 bg-gradient-to-br from-gold-50 to-transparent group-hover:opacity-100 dark:from-gold-950/20 dark:to-transparent"></div>

    <div class="relative">
        <div class="mb-4 text-4xl">{{ $icon }}</div>
        <h3 class="mb-2 text-lg font-bold text-zinc-900 dark:text-white">{{ $title }}</h3>
        <p class="mb-4 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $description }}</p>

        @if ($count > 0)
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gold-600 dark:text-gold-400">{{ $count }}+ varieties</span>
                <span class="inline-flex items-center gap-1 text-xs font-medium transition text-zinc-400 group-hover:text-gold-600 dark:group-hover:text-gold-400">
                    View
                    <svg xmlns="http://www.w3.org/2000/svg" class="transition-transform size-3 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </div>
        @endif
    </div>
</div>
