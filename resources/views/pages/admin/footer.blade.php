<?php

use App\Models\FooterSetting;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Footer Settings')] class extends Component {
    public string $copyrightText = '';
    public string $creditText = '';
    public string $creditUrl = '';

    public function mount(): void
    {
        $footer = FooterSetting::firstOrFail();
        $this->copyrightText = $footer->copyright_text ?? '';
        $this->creditText = $footer->credit_text ?? '';
        $this->creditUrl = $footer->credit_url ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'copyrightText' => ['nullable', 'string', 'max:255'],
            'creditText' => ['nullable', 'string', 'max:255'],
            'creditUrl' => ['nullable', 'string', 'max:255'],
        ]);

        FooterSetting::firstOrFail()->update([
            'copyright_text' => $this->copyrightText,
            'credit_text' => $this->creditText,
            'credit_url' => $this->creditUrl,
        ]);

        Flux::toast(
            heading: 'Footer saved',
            text: 'The footer settings have been updated successfully.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Footer Settings') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the footer content.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="save" class="mt-8 max-w-2xl space-y-8">

        <div>
            <flux:heading level="3" size="lg">{{ __('Copyright') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Copyright Text') }}</flux:label>
                <flux:input wire:model="copyrightText" type="text" />
            </flux:field>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Credits') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Credit Text') }}</flux:label>
                <flux:input wire:model="creditText" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Credit URL') }}</flux:label>
                <flux:input wire:model="creditUrl" type="text" />
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
