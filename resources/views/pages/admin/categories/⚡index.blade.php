<?php

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Layout('layouts.admin'), Title('Manage Categories')] class extends Component {
    use WithFileUploads, WithPagination;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public $image = null;

    public bool $is_active = true;

    public int $sort_order = 0;

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'description', 'image', 'is_active', 'sort_order']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order;
        $this->image = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->image) {
            if ($this->editingId) {
                $existing = Category::find($this->editingId);
                if ($existing?->image_path) {
                    Storage::disk('public')->delete($existing->image_path);
                }
            }
            $data['image_path'] = $this->image->store('categories', 'public');
        }

        if ($this->editingId) {
            Category::where('id', $this->editingId)->update($data);
            $this->dispatch('success', message: __('Category updated successfully.'));
        } else {
            Category::create($data);
            $this->dispatch('success', message: __('Category created successfully.'));
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'description', 'image', 'is_active', 'sort_order']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $category = Category::findOrFail($this->deletingId);

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->dispatch('success', message: __('Category deleted successfully.'));
    }

    public function with(): array
    {
        return [
            'categories' => Category::withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10),
            'totalCategories' => Category::count(),
            'activeCategories' => Category::where('is_active', true)->count(),
        ];
    }
}

?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <h1 class="text-3xl font-bold bg-linear-to-r from-amber-600 to-orange-800 bg-clip-text text-transparent">
            {{ __('Categories') }}
        </h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Manage your bakery product categories.') }}
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-amber-500 to-orange-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Categories') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $totalCategories }}</p>
            </div>
        </div>

        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-emerald-500 to-green-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Active Categories') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $activeCategories }}</p>
            </div>
        </div>

        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-orange-500 to-red-500 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Products') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $categories->sum('products_count') }}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-md overflow-hidden border border-zinc-200 dark:border-zinc-700">
        <div class="bg-linear-to-r from-zinc-50 to-slate-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wide">{{ __('Categories List') }}</h3>
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">({{ $categories->total() }} {{ __('total') }})</span>
            </div>
            <button
                wire:click="create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-linear-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white text-sm font-semibold rounded-xl shadow-lg transition-all cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('Add Category') }}
            </button>
        </div>

        <table class="w-full">
            <thead class="bg-zinc-100 dark:bg-zinc-800/70 border-b-2 border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Category') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Products') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Order') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse ($categories as $category)
                    <tr wire:key="category-{{ $category->id }}" class="group hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-all">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                @if ($category->image_path)
                                    <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-xl object-cover shadow-sm" />
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-base font-bold text-zinc-900 dark:text-white">{{ $category->name }}</div>
                                    @if ($category->description)
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ Str::limit($category->description, 50) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-600">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            @if ($category->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('Active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    {{ __('Inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $category->sort_order }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button
                                    wire:click="edit({{ $category->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    {{ __('Edit') }}
                                </button>
                                <button
                                    wire:click="confirmDelete({{ $category->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="p-4 bg-zinc-100 dark:bg-zinc-700 rounded-2xl">
                                    <svg class="w-16 h-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('No categories found') }}</p>
                                    <p class="mt-1 text-sm text-zinc-500">{{ __('Create your first category to get started.') }}</p>
                                </div>
                                <button
                                    wire:click="create"
                                    class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 bg-linear-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white text-sm font-semibold rounded-xl shadow-lg cursor-pointer"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    {{ __('Add Your First Category') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Showing') }} <span class="font-medium">{{ $categories->firstItem() }}</span> {{ __('to') }} <span class="font-medium">{{ $categories->lastItem() }}</span> {{ __('of') }} <span class="font-medium">{{ $categories->total() }}</span>
                </div>
                <div>{{ $categories->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="show = false"
            class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl max-w-lg w-full border border-zinc-200 dark:border-zinc-700 overflow-hidden"
            >
                {{-- Modal Header --}}
                <div class="bg-linear-to-r from-amber-600 to-orange-600 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            {{ $editingId ? __('Edit Category') : __('Add Category') }}
                        </h3>
                    </div>
                    <button
                        @click="show = false"
                        class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6">
                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                {{ __('Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                placeholder="{{ __('e.g. Bread, Cakes') }}"
                                class="w-full px-3 py-2.5 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all placeholder-zinc-400"
                            />
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                {{ __('Description') }}
                            </label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                placeholder="{{ __('Brief description of this category...') }}"
                                class="w-full px-3 py-2.5 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all placeholder-zinc-400"
                            ></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Image --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                {{ __('Image') }}
                            </label>
                            <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-700 hover:file:bg-amber-200 dark:file:bg-amber-900/30 dark:file:text-amber-300 cursor-pointer" />
                            @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <div wire:loading wire:target="image" class="mt-1 text-sm text-zinc-500">{{ __('Uploading...') }}</div>
                        </div>

                        {{-- Sort Order --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                {{ __('Sort Order') }}
                            </label>
                            <input
                                type="number"
                                wire:model="sort_order"
                                min="0"
                                class="w-full px-3 py-2.5 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all"
                            />
                            @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Active --}}
                        <label class="flex items-center gap-3 px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 cursor-pointer hover:border-amber-400 transition-all">
                            <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-amber-600 rounded-md border-zinc-300 dark:border-zinc-600 focus:ring-amber-500">
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Active') }}</span>
                        </label>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-zinc-200 dark:border-zinc-700">
                        <button
                            type="button"
                            @click="show = false"
                            class="px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors cursor-pointer"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition-colors cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="save">{{ $editingId ? __('Update') : __('Create') }}</span>
                            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div
        x-data="{ show: @entangle('showDeleteModal') }"
        x-show="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="show = false"
            class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 shadow-2xl ring-1 ring-zinc-950/5 dark:ring-white/10 transition-all"
            >
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/20">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Delete Category') }}</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Are you sure you want to delete this category? All products in this category will also be deleted. This action cannot be undone.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-zinc-200 dark:border-zinc-700">
                    <button
                        type="button"
                        @click="show = false"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all cursor-pointer"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="delete"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm hover:shadow transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
