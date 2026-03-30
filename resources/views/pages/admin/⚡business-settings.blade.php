<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin'), Title('Business Settings')] class extends Component {
    public string $businessName = '';

    public string $businessOwner = '';

    public string $contactNumber = '';

    public string $businessEmail = '';

    public string $businessAddress = '';

    public string $businessDescription = '';

    public string $businessHours = '';

    public function mount(): void
    {
        $this->businessName = Setting::get('business_name', '') ?? '';
        $this->businessOwner = Setting::get('business_owner', '') ?? '';
        $this->contactNumber = Setting::get('contact_number', '') ?? '';
        $this->businessEmail = Setting::get('business_email', '') ?? '';
        $this->businessAddress = Setting::get('business_address', '') ?? '';
        $this->businessDescription = Setting::get('business_description', '') ?? '';
        $this->businessHours = Setting::get('business_hours', '') ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'businessName' => ['required', 'string', 'max:150'],
            'businessOwner' => ['required', 'string', 'max:150'],
            'contactNumber' => ['required', 'string', 'max:30'],
            'businessEmail' => ['nullable', 'email', 'max:150'],
            'businessAddress' => ['nullable', 'string', 'max:500'],
            'businessDescription' => ['nullable', 'string', 'max:1000'],
            'businessHours' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        Setting::set('business_name', $validated['businessName']);
        Setting::set('business_owner', $validated['businessOwner']);
        Setting::set('contact_number', $validated['contactNumber']);
        Setting::set('business_email', $validated['businessEmail'] ?? null);
        Setting::set('business_address', $validated['businessAddress'] ?? null);
        Setting::set('business_description', $validated['businessDescription'] ?? null);
        Setting::set('business_hours', $validated['businessHours'] ?? null);

        $this->dispatch('settings-saved');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
        <h1 class="text-2xl sm:text-3xl font-bold bg-linear-to-r from-amber-600 to-orange-800 bg-clip-text text-transparent">
            {{ __('Business Settings') }}
        </h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Manage your bakery\'s public business information displayed to customers.') }}
        </p>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <form wire:submit="save" class="max-w-2xl space-y-6">

            {{-- Business Identity --}}
            <div>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Business Identity</h2>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Business Name') }}</flux:label>
                        <flux:input wire:model="businessName" type="text" placeholder="e.g. Sweet Bites Bakery" />
                        <flux:error name="businessName" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Owner / Proprietor') }}</flux:label>
                        <flux:input wire:model="businessOwner" type="text" placeholder="e.g. Maria Santos" />
                        <flux:error name="businessOwner" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Business Description') }}</flux:label>
                        <flux:textarea
                            wire:model="businessDescription"
                            rows="3"
                            placeholder="Brief description about your bakery..."
                        />
                        <flux:error name="businessDescription" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            {{-- Contact Information --}}
            <div>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Contact Information</h2>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Contact Number') }}</flux:label>
                        <flux:input wire:model="contactNumber" type="text" placeholder="e.g. +63 917 123 4567" />
                        <flux:error name="contactNumber" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Address') }} <flux:text class="inline text-zinc-400">({{ __('optional') }})</flux:text></flux:label>
                        <flux:input wire:model="businessEmail" type="email" placeholder="e.g. info@sweetbites.com" />
                        <flux:error name="businessEmail" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            {{-- Location & Hours --}}
            <div>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Location & Hours</h2>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Store Address') }} <flux:text class="inline text-zinc-400">({{ __('optional') }})</flux:text></flux:label>
                        <flux:textarea
                            wire:model="businessAddress"
                            rows="2"
                            placeholder="e.g. 123 Rizal Street, Brgy. San Jose, Marikina City, 1800"
                        />
                        <flux:error name="businessAddress" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Business Hours') }} <flux:text class="inline text-zinc-400">({{ __('optional') }})</flux:text></flux:label>
                        <flux:input wire:model="businessHours" type="text" placeholder="e.g. Mon–Sat: 6:00 AM – 8:00 PM" />
                        <flux:error name="businessHours" />
                    </flux:field>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('Save Settings') }}
                </flux:button>
                <x-action-message on="settings-saved">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </div>
</div>
