<?php

use App\Models\HeroSection;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Hero Section')] class extends Component {
    public string $heading = '';
    public string $subheading = '';
    public string $ctaPrimaryText = '';
    public string $ctaPrimaryUrl = '';
    public string $ctaSecondaryText = '';
    public string $ctaSecondaryUrl = '';
    public string $profileImage = '';
    public array $stats = [];

    public function mount(): void
    {
        $hero = HeroSection::firstOrFail();
        $this->heading = $hero->heading ?? '';
        $this->subheading = $hero->subheading ?? '';
        $this->ctaPrimaryText = $hero->cta_primary_text ?? '';
        $this->ctaPrimaryUrl = $hero->cta_primary_url ?? '';
        $this->ctaSecondaryText = $hero->cta_secondary_text ?? '';
        $this->ctaSecondaryUrl = $hero->cta_secondary_url ?? '';
        $this->profileImage = $hero->profile_image ?? '';
        $this->stats = $hero->stats ?? [];
    }

    public function addStat(): void
    {
        $this->stats[] = ['number' => '', 'label' => ''];
    }

    public function removeStat(int $index): void
    {
        unset($this->stats[$index]);
        $this->stats = array_values($this->stats);
    }

    public function save(): void
    {
        $this->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:65535'],
            'ctaPrimaryText' => ['nullable', 'string', 'max:255'],
            'ctaPrimaryUrl' => ['nullable', 'string', 'max:255'],
            'ctaSecondaryText' => ['nullable', 'string', 'max:255'],
            'ctaSecondaryUrl' => ['nullable', 'string', 'max:255'],
            'profileImage' => ['nullable', 'string', 'max:255'],
            'stats' => ['nullable', 'array'],
        ]);

        HeroSection::firstOrFail()->update([
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'cta_primary_text' => $this->ctaPrimaryText,
            'cta_primary_url' => $this->ctaPrimaryUrl,
            'cta_secondary_text' => $this->ctaSecondaryText,
            'cta_secondary_url' => $this->ctaSecondaryUrl,
            'profile_image' => $this->profileImage,
            'stats' => $this->stats,
        ]);

        Flux::toast(
            heading: 'Hero section saved',
            text: 'The hero section has been updated successfully.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Hero Section') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the hero section content.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="save" class="mt-8 max-w-2xl space-y-8">

        <div>
            <flux:heading level="3" size="lg">{{ __('Main Content') }}</flux:heading>
            <flux:text class="mt-1 mb-4">{{ __('The main heading and subheading text.') }}</flux:text>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Heading') }}</flux:label>
                    <flux:input wire:model="heading" type="text" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Subheading') }}</flux:label>
                    <flux:textarea wire:model="subheading" rows="3" />
                </flux:field>
            </div>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Call to Action Buttons') }}</flux:heading>
            <flux:text class="mt-1 mb-4">{{ __('Buttons displayed in the hero section.') }}</flux:text>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Primary Button Text') }}</flux:label>
                    <flux:input wire:model="ctaPrimaryText" type="text" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Primary Button URL') }}</flux:label>
                    <flux:input wire:model="ctaPrimaryUrl" type="text" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Button Text') }}</flux:label>
                    <flux:input wire:model="ctaSecondaryText" type="text" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Secondary Button URL') }}</flux:label>
                    <flux:input wire:model="ctaSecondaryUrl" type="text" />
                </flux:field>
            </div>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Profile Image') }}</flux:heading>
            <flux:text class="mt-1 mb-4">{{ __('Path to the hero profile image.') }}</flux:text>

            <flux:field>
                <flux:label>{{ __('Image Path') }}</flux:label>
                <flux:input wire:model="profileImage" type="text" />
            </flux:field>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading level="3" size="lg">{{ __('Stats') }}</flux:heading>
                    <flux:text class="mt-1 mb-4">{{ __('Statistics displayed in the hero section.') }}</flux:text>
                </div>
                <flux:button variant="primary" size="sm" wire:click="addStat" type="button">
                    {{ __('Add Stat') }}
                </flux:button>
            </div>

            <div class="space-y-3">
                @foreach($stats as $index => $stat)
                    <div class="flex items-end gap-3 rounded-lg border p-4">
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Number') }}</flux:label>
                            <flux:input wire:model="stats.{{ $index }}.number" type="text" />
                        </flux:field>

                        <flux:field class="flex-1">
                            <flux:label>{{ __('Label') }}</flux:label>
                            <flux:input wire:model="stats.{{ $index }}.label" type="text" />
                        </flux:field>

                        <flux:button variant="ghost" size="sm" wire:click="removeStat({{ $index }})" type="button" icon="trash" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save') }}</span>
                <span wire:loading>{{ __('Saving...') }}</span>
            </flux:button>
        </div>

    </form>
</section>
