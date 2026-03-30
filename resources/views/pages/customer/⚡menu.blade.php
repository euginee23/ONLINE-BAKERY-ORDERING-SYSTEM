<?php

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.customer'), Title('Browse Menu')] class extends Component {
    #[Url(as: 'category')]
    public string $categoryFilter = '';

    #[Url(as: 'q')]
    public string $search = '';

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if (! $product->isInStock()) {
            $this->dispatch('notify', type: 'error', message: 'Sorry, this product is out of stock.');

            return;
        }

        $cart = app(CartService::class);
        $cart->add($productId);

        $this->dispatch('notify', type: 'success', message: "{$product->name} added to cart!");
        $this->dispatch('cart-updated');
    }

    public function with(): array
    {
        $query = Product::with('category')->available();

        if ($this->categoryFilter) {
            $query->whereHas('category', fn ($q) => $q->where('id', $this->categoryFilter));
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return [
            'products' => $query->orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Browse Menu</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Fresh from the oven, ready to order.</p>
        </div>
        <a
            href="{{ route('customer.checkout') }}"
            wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
        >
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            View Cart
            @php $cartCount = count(session()->get('cart', [])); @endphp
            @if($cartCount > 0)
                <span class="inline-flex items-center justify-center rounded-full px-1.5 py-0.5 text-xs font-bold bg-white text-gold-800">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    {{-- Search + Category Filter --}}
    <div class="flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search products..."
                class="w-full pl-9 pr-4 py-2 text-sm border rounded-lg border-zinc-300 bg-white text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-gold-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
            />
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                wire:click="$set('categoryFilter', '')"
                @class([
                    'px-3 py-1.5 text-sm font-medium transition rounded-lg',
                    'bg-gold-700 text-white dark:bg-gold-500 dark:text-zinc-900' => $categoryFilter === '',
                    'border border-zinc-300 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-800' => $categoryFilter !== '',
                ])
            >
                All
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="$set('categoryFilter', '{{ $cat->id }}')"
                    @class([
                        'px-3 py-1.5 text-sm font-medium transition rounded-lg',
                        'bg-gold-700 text-white dark:bg-gold-500 dark:text-zinc-900' => $categoryFilter === (string) $cat->id,
                        'border border-zinc-300 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-800' => $categoryFilter !== (string) $cat->id,
                    ])
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Product Grid --}}
    @if($products->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="mb-4 text-6xl">🍞</div>
            <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">No products found</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Try adjusting your search or filter.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <div class="group flex flex-col bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    {{-- Product Image --}}
                    <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-700 aspect-square">
                        @if($product->image_path)
                            <img
                                src="{{ Storage::url($product->image_path) }}"
                                alt="{{ $product->name }}"
                                class="object-cover w-full h-full transition duration-300 group-hover:scale-105"
                            />
                        @else
                            <div class="flex items-center justify-center w-full h-full text-5xl">🍞</div>
                        @endif

                        {{-- Stock badge --}}
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full dark:text-orange-300 dark:bg-orange-900/40">
                                Only {{ $product->stock }} left
                            </span>
                        @endif
                        @if(! $product->isInStock())
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50">
                                <span class="px-3 py-1 text-sm font-bold text-white bg-red-600 rounded-full">Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
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

                            <button
                                wire:click="addToCart({{ $product->id }})"
                                @disabled(! $product->isInStock())
                                @class([
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                                    'bg-gold-700 text-white hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400' => $product->isInStock(),
                                    'bg-zinc-100 text-zinc-400 cursor-not-allowed dark:bg-zinc-700 dark:text-zinc-500' => ! $product->isInStock(),
                                ])
                            >
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
