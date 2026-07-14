<?php

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Base Color Guest settings')] class extends Component {
    public string $primary = '';
    public string $secondary = '';

    /**
     * Mount the component with the current theme values.
     */
    public function mount(): void
    {
        $this->primary = config('theming.primary');
        $this->secondary = config('theming.secondary');
    }

    /**
     * Save the selected theme colors to a JSON file.
     *
     * When migrating to a database, replace file_put_contents with
     * an Eloquent model save — everything else stays the same.
     */
    public function save(): void
    {
        $this->validate([
            'primary' => ['required', 'string'],
            'secondary' => ['required', 'string'],
        ]);

        $path = storage_path('app/settings/theme.json');

        file_put_contents($path, json_encode([
            'primary' => $this->primary,
            'secondary' => $this->secondary,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        Flux::toast(variant: 'success', text: __('Base Color Guest colors updated.'));
    }

    /**
     * The list of predefined color options available in the picker.
     */
    public function colorOptions(): array
    {
        return config('theming.options', []);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Base Color Guest') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Base Color Guest')" :subheading="__('Choose the primary and secondary colors for your guest site.')">
        <form wire:submit="save" class="my-6 w-full space-y-8">
            {{-- Primary Color --}}
            <div>
                <flux:heading level="3" size="lg">{{ __('Primary Color') }}</flux:heading>
                <flux:text class="mt-1 mb-4">{{ __('Used for buttons, links, accent elements, and interactive highlights across both the admin panel and guest site.') }}</flux:text>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($this->colorOptions() as $option)
                        <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border p-4 has-[:checked]:ring-2 has-[:checked]:ring-accent has-[:checked]:border-accent">
                            <input type="radio" name="primary" value="{{ $option['value'] }}" wire:model="primary" class="sr-only">
                            <span class="inline-block size-8 shrink-0 rounded-full border" style="background-color: {{ $option['value'] }}"></span>
                            <span class="text-sm font-medium">{{ $option['label'] }}</span>
                            <span class="ml-auto text-xs text-zinc-400" style="font-family: monospace">{{ $option['value'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Secondary Color --}}
            <div>
                <flux:heading level="3" size="lg">{{ __('Secondary Color') }}</flux:heading>
                <flux:text class="mt-1 mb-4">{{ __('Used for headings, section titles, and supporting visual elements on the guest site.') }}</flux:text>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($this->colorOptions() as $option)
                        <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border p-4 has-[:checked]:ring-2 has-[:checked]:ring-accent has-[:checked]:border-accent">
                            <input type="radio" name="secondary" value="{{ $option['value'] }}" wire:model="secondary" class="sr-only">
                            <span class="inline-block size-8 shrink-0 rounded-full border" style="background-color: {{ $option['value'] }}"></span>
                            <span class="text-sm font-medium">{{ $option['label'] }}</span>
                            <span class="ml-auto text-xs text-zinc-400" style="font-family: monospace">{{ $option['value'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
