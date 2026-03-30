<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

new class extends Component
{
    public string $categoryFilter = '';

    public int $perPage = 8;

    public bool $hasMore = true;

    public function filterCategory(string $categoryId): void
    {
        $this->categoryFilter = $categoryId;
        $this->perPage = 8;
        $this->hasMore = true;
    }

    public function loadMore(): void
    {
        $this->perPage += 8;
    }

    public function with(): array
    {
        $query = Product::with('category')->available()->inStock();

        if ($this->categoryFilter) {
            $query->whereHas('category', fn ($q) => $q->where('id', $this->categoryFilter));
        }

        $total = (clone $query)->count();
        $products = $query->orderBy('name')->take($this->perPage)->get();
        $this->hasMore = $products->count() < $total;

        return [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
};
?>

<div>
    {{-- Category Tabs --}}
    <div class="flex flex-wrap justify-center gap-2 mb-8">
        <button
            wire:click="filterCategory('')"
            @class([
                'px-5 py-2 text-sm font-semibold rounded-full transition',
                'bg-gold-700 text-white shadow-md dark:bg-gold-500 dark:text-zinc-900' => $categoryFilter === '',
                'border border-zinc-300 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' => $categoryFilter !== '',
            ])
        >
            All
        </button>
        @foreach($categories as $cat)
            <button
                wire:click="filterCategory('{{ $cat->id }}')"
                @class([
                    'px-5 py-2 text-sm font-semibold rounded-full transition',
                    'bg-gold-700 text-white shadow-md dark:bg-gold-500 dark:text-zinc-900' => $categoryFilter === (string) $cat->id,
                    'border border-zinc-300 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' => $categoryFilter !== (string) $cat->id,
                ])
            >
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    {{-- Product Grid --}}
    @if($products->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="mb-4 text-5xl">🍞</div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No products available right now.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <div wire:key="product-{{ $product->id }}" class="group flex flex-col bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    {{-- Image --}}
                    <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-700 aspect-square">
                        @if($product->image_path)
                            <img
                                src="{{ Storage::url($product->image_path) }}"
                                alt="{{ $product->name }}"
                                loading="lazy"
                                class="object-cover w-full h-full transition duration-300 group-hover:scale-105"
                            />
                        @else
                            <div class="flex items-center justify-center w-full h-full text-5xl">🍞</div>
                        @endif

                        @if($product->stock <= 5)
                            <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-semibold text-orange-700 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/40 rounded-full">
                                Only {{ $product->stock }} left
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex flex-col flex-1 p-4 gap-3">
                        <div class="flex-1">
                            <span class="text-xs font-medium text-gold-700 dark:text-gold-400">{{ $product->category?->name }}</span>
                            <h3 class="mt-0.5 text-sm font-semibold text-zinc-900 dark:text-white line-clamp-2">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $product->description }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-zinc-900 dark:text-white">₱{{ number_format($product->price, 2) }}</span>

                            @auth
                                <a
                                    href="{{ route('customer.menu') }}"
                                    wire:navigate
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition bg-gold-700 text-white hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Order
                                </a>
                            @else
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-register-modal')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition bg-gold-700 text-white hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Order
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Lazy Load sentinel --}}
        @if($hasMore)
            <div
                wire:intersect.threshold.10="loadMore"
                class="flex justify-center py-8"
            >
                <div class="flex items-center gap-2 text-sm text-zinc-400 dark:text-zinc-500">
                    <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Loading more...
                </div>
            </div>
        @else
            <div class="pt-8 text-center">
                <p class="text-xs text-zinc-400 dark:text-zinc-600">✓ All products shown</p>
            </div>
        @endif
    @endif
</div>