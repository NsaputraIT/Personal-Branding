<?php

use App\Models\Site;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Site settings')] class extends Component {
    public string $siteName = '';

    public function mount(): void
    {
        $this->siteName = Site::firstOrFail()->site_name;
    }

    /**
     * Persist the site name.
     */
    public function save(): void
    {
        $this->validate([
            'siteName' => ['required', 'string', 'max:255'],
        ]);

        Site::firstOrFail()->update(['site_name' => $this->siteName]);

        Flux::toast(variant: 'success', text: __('Site name updated.'));
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Site') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the site name displayed in the guest site header.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="save" class="mt-8 max-w-2xl">
        <flux:heading level="3" size="lg">{{ __('Site Name') }}</flux:heading>
        <flux:text class="mt-1 mb-4">{{ __('The name displayed in the header logo area.') }}</flux:text>

        <div class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model="siteName" type="text" required />
            </div>

            <flux:button variant="primary" type="submit">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</section>
