<?php

use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Addresses')] class extends Component {
    public string $label = '';

    public string $address = '';

    public bool $showAddForm = false;

    public function mount(): void
    {
        $this->showAddForm = Auth::user()->addresses()->doesntExist();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'address' => ['required', 'string', 'max:500'],
            'label' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function addAddress(): void
    {
        $validated = $this->validate($this->rules());

        Auth::user()->addresses()->create($validated);

        $this->reset('label', 'address');
        $this->showAddForm = false;

        $this->dispatch('address-saved');
    }

    public function setDefault(int $id): void
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
    }

    public function deleteAddress(int $id): void
    {
        Auth::user()->addresses()->where('id', $id)->delete();

        if (Auth::user()->addresses()->doesntExist()) {
            $this->showAddForm = true;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'addresses' => Auth::user()->addresses()->orderByDesc('is_default')->latest()->get(),
        ];
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Addresses') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Addresses')" :subheading="__('Manage your saved delivery addresses')">
        <div class="my-6 space-y-4">

            {{-- Existing addresses --}}
            @forelse ($addresses as $addr)
                <div class="flex items-start justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            @if ($addr->label)
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $addr->label }}</span>
                            @endif
                            @if ($addr->is_default)
                                <flux:badge color="lime" size="sm">{{ __('Default') }}</flux:badge>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $addr->address }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        @unless ($addr->is_default)
                            <flux:button
                                size="sm"
                                variant="ghost"
                                wire:click="setDefault({{ $addr->id }})"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Set default') }}
                            </flux:button>
                        @endunless
                        <flux:button
                            size="sm"
                            variant="ghost"
                            wire:click="deleteAddress({{ $addr->id }})"
                            wire:loading.attr="disabled"
                            wire:confirm="{{ __('Delete this address?') }}"
                            icon="trash"
                        />
                    </div>
                </div>
            @empty
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No saved addresses yet.') }}</p>
            @endforelse

            {{-- Toggle add form --}}
            @unless ($showAddForm)
                <flux:button
                    variant="outline"
                    icon="plus"
                    wire:click="$set('showAddForm', true)"
                >
                    {{ __('Add address') }}
                </flux:button>
            @endunless

            {{-- Add address form --}}
            @if ($showAddForm)
                <form
                    wire:submit="addAddress"
                    class="space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700"
                >
                    <flux:heading size="sm">{{ __('New address') }}</flux:heading>

                    <flux:field>
                        <flux:label>{{ __('Label') }} <flux:text class="inline text-zinc-400">({{ __('optional') }})</flux:text></flux:label>
                        <flux:input
                            wire:model="label"
                            type="text"
                            placeholder="{{ __('e.g. Home, Work') }}"
                            autocomplete="off"
                        />
                        <flux:error name="label" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Address') }}</flux:label>
                        <flux:textarea
                            wire:model="address"
                            rows="3"
                            placeholder="{{ __('Street, City, Province, ZIP') }}"
                            required
                        />
                        <flux:error name="address" />
                    </flux:field>

                    <div class="flex items-center gap-3">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            {{ __('Save address') }}
                        </flux:button>

                        @if ($addresses->isNotEmpty())
                            <flux:button
                                type="button"
                                variant="ghost"
                                wire:click="$set('showAddForm', false)"
                            >
                                {{ __('Cancel') }}
                            </flux:button>
                        @endif

                        <x-action-message on="address-saved">
                            {{ __('Saved.') }}
                        </x-action-message>
                    </div>
                </form>
            @endif
        </div>
    </x-pages::settings.layout>
</section>
