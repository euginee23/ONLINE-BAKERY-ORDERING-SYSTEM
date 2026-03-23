<?php

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Manage Categories')] class extends Component {
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
        } else {
            Category::create($data);
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
    }

    public function with(): array
    {
        return [
            'categories' => Category::withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10),
        ];
    }
}

?>

<section class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Categories') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500">{{ __('Manage your bakery product categories.') }}</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="create">
                {{ __('Add Category') }}
            </flux:button>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Products') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Order') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                @if ($category->image_path)
                                    <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-lg object-cover" />
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="squares-2x2" class="size-5 text-zinc-400" />
                                    </div>
                                @endif
                                <div>
                                    <flux:heading size="sm">{{ $category->name }}</flux:heading>
                                    @if ($category->description)
                                        <flux:text class="text-xs text-zinc-500">{{ Str::limit($category->description, 50) }}</flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $category->products_count }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$category->is_active ? 'green' : 'red'">
                                {{ $category->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $category->sort_order }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" icon="pencil" wire:click="edit({{ $category->id }})" />
                                <flux:button size="sm" variant="subtle" icon="trash" wire:click="confirmDelete({{ $category->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            <flux:text class="text-zinc-500">{{ __('No categories found.') }}</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div>{{ $categories->links() }}</div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editingId ? __('Edit Category') : __('Add Category') }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="e.g. Bread, Cakes" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" rows="3" placeholder="Brief description of this category..." />
                        <flux:error name="description" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Image') }}</flux:label>
                        <input type="file" wire:model="image" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300" />
                        <flux:error name="image" />
                        <div wire:loading wire:target="image" class="mt-1 text-sm text-zinc-500">{{ __('Uploading...') }}</div>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Sort Order') }}</flux:label>
                        <flux:input type="number" wire:model="sort_order" min="0" />
                        <flux:error name="sort_order" />
                    </flux:field>

                    <flux:checkbox wire:model="is_active" label="{{ __('Active') }}" />

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
                <flux:heading size="lg">{{ __('Delete Category') }}</flux:heading>
                <flux:text>{{ __('Are you sure you want to delete this category? All products in this category will also be deleted. This action cannot be undone.') }}</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button variant="subtle" wire:click="$set('showDeleteModal', false)">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</section>
