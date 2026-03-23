<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Manage Products')] class extends Component {
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public int $stock = 0;

    public ?int $category_id = null;

    public $image = null;

    public bool $is_available = true;

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'description', 'price', 'stock', 'category_id', 'image', 'is_available']);
        $this->is_available = true;
        $this->stock = 0;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->is_available = $product->is_available;
        $this->image = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'is_available' => $this->is_available,
        ];

        if ($this->image) {
            if ($this->editingId) {
                $existing = Product::find($this->editingId);
                if ($existing?->image_path) {
                    Storage::disk('public')->delete($existing->image_path);
                }
            }
            $data['image_path'] = $this->image->store('products', 'public');
        }

        if ($this->editingId) {
            Product::where('id', $this->editingId)->update($data);
        } else {
            Product::create($data);
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'description', 'price', 'stock', 'category_id', 'image', 'is_available']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $product = Product::findOrFail($this->deletingId);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function with(): array
    {
        $query = Product::with('category');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        return [
            'products' => $query->latest()->paginate(10),
            'categories' => Category::orderBy('sort_order')->get(),
        ];
    }
}

?>

<section class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Products') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500">{{ __('Manage your bakery products and inventory.') }}</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="create">
                {{ __('Add Product') }}
            </flux:button>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="sm:w-64">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search products...') }}" icon="magnifying-glass" />
            </div>
            <div class="sm:w-48">
                <flux:select wire:model.live="categoryFilter" placeholder="{{ __('All Categories') }}">
                    <flux:select.option value="">{{ __('All Categories') }}</flux:select.option>
                    @foreach ($categories as $category)
                        <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Product') }}</flux:table.column>
                <flux:table.column>{{ __('Category') }}</flux:table.column>
                <flux:table.column>{{ __('Price') }}</flux:table.column>
                <flux:table.column>{{ __('Stock') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($products as $product)
                    <flux:table.row :key="$product->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                @if ($product->image_path)
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover" />
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="shopping-bag" class="size-5 text-zinc-400" />
                                    </div>
                                @endif
                                <div>
                                    <flux:heading size="sm">{{ $product->name }}</flux:heading>
                                    @if ($product->description)
                                        <flux:text class="text-xs text-zinc-500">{{ Str::limit($product->description, 40) }}</flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $product->category?->name ?? '—' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>₱{{ number_format($product->price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$product->stock > 0 ? 'zinc' : 'red'">
                                {{ $product->stock }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$product->is_available ? 'green' : 'red'">
                                {{ $product->is_available ? __('Available') : __('Unavailable') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="edit({{ $product->id }})" />
                                <flux:button size="sm" variant="subtle" icon="trash" wire:click="confirmDelete({{ $product->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">
                            <flux:text class="text-zinc-500">{{ __('No products found.') }}</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div>{{ $products->links() }}</div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editingId ? __('Edit Product') : __('Add Product') }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="e.g. Pandesal, Ube Cake" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <flux:select wire:model="category_id" placeholder="{{ __('Select a category') }}">
                            @foreach ($categories as $category)
                                <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="category_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" rows="3" placeholder="Brief description of this product..." />
                        <flux:error name="description" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Price (₱)') }}</flux:label>
                            <flux:input type="number" wire:model="price" step="0.01" min="0" placeholder="0.00" />
                            <flux:error name="price" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Stock') }}</flux:label>
                            <flux:input type="number" wire:model="stock" min="0" placeholder="0" />
                            <flux:error name="stock" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Image') }}</flux:label>
                        <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300" />
                        <flux:error name="image" />
                        <div wire:loading wire:target="image" class="mt-1 text-sm text-zinc-500">{{ __('Uploading...') }}</div>
                    </flux:field>

                    <flux:checkbox wire:model="is_available" label="{{ __('Available for ordering') }}" />

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button variant="subtle" wire:click="$set('showModal', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            {{ $editingId ? __('Update') : __('Create') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('Delete Product') }}</flux:heading>
                <flux:text>{{ __('Are you sure you want to delete this product? This action cannot be undone.') }}</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button variant="subtle" wire:click="$set('showDeleteModal', false)">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</section>
