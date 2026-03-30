<?php

use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.settings'), Title('Addresses')] class extends Component {
    public string $label = '';

    public string $houseStreet = '';

    public string $barangay = '';

    public string $city = '';

    public string $province = '';

    public string $region = '';

    public string $zipCode = '';

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
            'label' => ['nullable', 'string', 'max:100'],
            'houseStreet' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'zipCode' => ['required', 'digits:4'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'houseStreet.required' => 'The house/street field is required.',
            'zipCode.digits' => 'The ZIP code must be exactly 4 digits.',
        ];
    }

    public function addAddress(): void
    {
        $validated = $this->validate($this->rules());

        Auth::user()->addresses()->create([
            'label' => $validated['label'] ?: null,
            'house_street' => $validated['houseStreet'],
            'barangay' => $validated['barangay'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'region' => $validated['region'] ?: null,
            'zip_code' => $validated['zipCode'],
        ]);

        $this->reset('label', 'houseStreet', 'barangay', 'city', 'province', 'region', 'zipCode');
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
                        <p class="mt-1 text-sm text-zinc-800 dark:text-zinc-200">{{ $addr->house_street }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Brgy. {{ $addr->barangay }}, {{ $addr->city }}, {{ $addr->province }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-500">
                            @if ($addr->region) {{ $addr->region }},  @endif{{ $addr->zip_code }}
                        </p>
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
                        <flux:label>{{ __('House No. / Street') }}</flux:label>
                        <flux:input
                            wire:model="houseStreet"
                            type="text"
                            placeholder="{{ __('e.g. 123 Rizal Street') }}"
                            autocomplete="off"
                        />
                        <flux:error name="houseStreet" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Barangay') }}</flux:label>
                        <flux:input
                            wire:model="barangay"
                            type="text"
                            placeholder="{{ __('e.g. Barangay San Jose') }}"
                            autocomplete="off"
                        />
                        <flux:error name="barangay" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field>
                            <flux:label>{{ __('City / Municipality') }}</flux:label>
                            <flux:input
                                wire:model="city"
                                type="text"
                                placeholder="{{ __('e.g. Marikina City') }}"
                                autocomplete="off"
                            />
                            <flux:error name="city" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Province') }}</flux:label>
                            <flux:input
                                wire:model="province"
                                type="text"
                                placeholder="{{ __('e.g. Metro Manila') }}"
                                autocomplete="off"
                            />
                            <flux:error name="province" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field>
                            <flux:label>{{ __('Region') }} <flux:text class="inline text-zinc-400">({{ __('optional') }})</flux:text></flux:label>
                            <flux:input
                                wire:model="region"
                                type="text"
                                placeholder="{{ __('e.g. NCR') }}"
                                autocomplete="off"
                            />
                            <flux:error name="region" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('ZIP Code') }}</flux:label>
                            <flux:input
                                wire:model="zipCode"
                                type="text"
                                placeholder="{{ __('e.g. 1800') }}"
                                maxlength="4"
                                autocomplete="off"
                            />
                            <flux:error name="zipCode" />
                        </flux:field>
                    </div>

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
