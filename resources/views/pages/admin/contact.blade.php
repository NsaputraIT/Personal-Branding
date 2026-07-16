<?php

use App\Models\ContactInfo;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Contact Section')] class extends Component {
    public string $heading = '';
    public string $subheading = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $mapUrl = '';
    public string $mapText = '';

    public function mount(): void
    {
        $contact = ContactInfo::firstOrFail();
        $this->heading = $contact->heading ?? '';
        $this->subheading = $contact->subheading ?? '';
        $this->email = $contact->email ?? '';
        $this->phone = $contact->phone ?? '';
        $this->address = $contact->address ?? '';
        $this->mapUrl = $contact->map_url ?? '';
        $this->mapText = $contact->map_text ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:65535'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'mapUrl' => ['nullable', 'string', 'max:255'],
            'mapText' => ['nullable', 'string', 'max:255'],
        ]);

        ContactInfo::firstOrFail()->update([
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'map_url' => $this->mapUrl,
            'map_text' => $this->mapText,
        ]);

        Flux::toast(
            heading: 'Contact section saved',
            text: 'The contact section has been updated successfully.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Contact Section') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the contact section content.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="save" class="mt-8 max-w-2xl space-y-8">

        <div>
            <flux:heading level="3" size="lg">{{ __('Section Header') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Heading') }}</flux:label>
                <flux:input wire:model="heading" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Subheading') }}</flux:label>
                <flux:textarea wire:model="subheading" rows="2" />
            </flux:field>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Contact Details') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input wire:model="email" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Phone') }}</flux:label>
                <flux:input wire:model="phone" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Address') }}</flux:label>
                <flux:input wire:model="address" type="text" />
            </flux:field>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Map') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Map URL') }}</flux:label>
                <flux:input wire:model="mapUrl" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Map Link Text') }}</flux:label>
                <flux:input wire:model="mapText" type="text" />
            </flux:field>
        </div>

        <div class="flex gap-2">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save') }}</span>
                <span wire:loading>{{ __('Saving...') }}</span>
            </flux:button>
        </div>

    </form>
</section>
