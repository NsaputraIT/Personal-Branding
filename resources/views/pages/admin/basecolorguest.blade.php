<?php

use App\Services\BaseColorGuestManager;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Base Color Guest settings')] class extends Component {
    public string $primary = '';
    public string $secondary = '';

    private BaseColorGuestManager $manager;

    public function boot(BaseColorGuestManager $manager): void
    {
        $this->manager = $manager;
    }

    public function mount(): void
    {
        $this->primary = $this->manager->get('primary');
        $this->secondary = $this->manager->get('secondary');
    }

    /**
     * Persist the selected colours to a PHP config override file.
     *
     * When migrating to a database:
     *   1. Replace file_put_contents with an Eloquent upsert.
     *   2. BaseColorGuestManager::get() swaps the getOverrides() call for a DB query.
     */
    public function save(): void
    {
        $this->validate([
            'primary' => ['required', 'string'],
            'secondary' => ['required', 'string'],
        ]);

        // Persist overrides so they survive across requests
        $overridePath = storage_path('app/settings/basecolorguest-override.php');
        file_put_contents(
            $overridePath,
            '<?php return ' . var_export([
                'primary' => $this->primary,
                'secondary' => $this->secondary,
            ], true) . ';' . PHP_EOL,
        );

        // Refresh the in-memory config so the page preview reflects the save
        config(['basecolorguest.primary' => $this->primary]);
        config(['basecolorguest.secondary' => $this->secondary]);

        Flux::toast(variant: 'success', text: __('Base Color Guest colors updated.'));
    }

    public function colorOptions(): array
    {
        return $this->manager->options();
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Base Color Guest') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Customise the colours used across your admin panel and guest site') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <div class="mt-8 grid gap-10 lg:grid-cols-5">
        {{-- Picker column --}}
        <div class="lg:col-span-3">
            <form wire:submit="save" class="space-y-10">
                {{-- Primary Color --}}
                <div>
                    <flux:heading level="3" size="lg">{{ __('Primary Color') }}</flux:heading>
                    <flux:text class="mt-1 mb-4">{{ __('Used for buttons, links, accent elements, and interactive highlights across both the admin panel and guest site.') }}</flux:text>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($this->colorOptions() as $option)
                            <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border p-4 has-[:checked]:ring-2 has-[:checked]:ring-accent has-[:checked]:border-accent">
                                <input type="radio" name="primary" value="{{ $option['value'] }}" wire:model.live="primary" class="sr-only">
                                <span class="inline-block size-8 shrink-0 rounded-full border" style="background-color: {{ $option['value'] }}"></span>
                                <span class="text-sm font-medium">{{ $option['label'] }}</span>
                                <span class="ml-auto hidden text-xs text-zinc-400 md:inline" style="font-family: monospace">{{ $option['value'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Secondary Color --}}
                <div class="mb-8">
                    <flux:heading level="3" size="lg">{{ __('Secondary Color') }}</flux:heading>
                    <flux:text class="mt-1 mb-4">{{ __('Used for headings, section titles, and supporting visual elements on the guest site.') }}</flux:text>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($this->colorOptions() as $option)
                            <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border p-4 has-[:checked]:ring-2 has-[:checked]:ring-accent has-[:checked]:border-accent">
                                <input type="radio" name="secondary" value="{{ $option['value'] }}" wire:model.live="secondary" class="sr-only">
                                <span class="inline-block size-8 shrink-0 rounded-full border" style="background-color: {{ $option['value'] }}"></span>
                                <span class="text-sm font-medium">{{ $option['label'] }}</span>
                                <span class="ml-auto hidden text-xs text-zinc-400 md:inline" style="font-family: monospace">{{ $option['value'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <flux:button variant="primary" type="submit">
                        {{ __('Save') }}
                    </flux:button>
                </div>
                <div class="my-6">
                    <flux:separator variant="subtle" />
                </div>

            </form>
        </div>

        {{-- Live preview column --}}
        <div class="lg:col-span-2">
            <flux:heading level="3" size="lg" class="mb-4">{{ __('Preview') }}</flux:heading>
            <flux:text class="mb-6">{{ __('See how your selected colours will look on the guest frontend and admin panel. The branding colours stay the same regardless of Light or Dark Appearance.') }}</flux:text>

            <div class="space-y-4 rounded-xl border bg-white p-6">
                {{-- Sample heading --}}
                <h3 style="color: {{ $this->secondary }}; font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem;">
                    {{ __('Sample Heading') }}
                </h3>
                <p style="color: #666; font-size: 0.875rem; margin: 0 0 1rem;">
                    {{ __('This is how your colour choices will appear on the guest frontend.') }}
                </p>

                {{-- Sample button --}}
                <button style="background-color: {{ $this->primary }}; color: #fff; padding: 0.5rem 1.5rem; border-radius: 9999px; border: none; font-weight: 500; cursor: default;">
                    {{ __('Sample Button') }}
                </button>

                {{-- Sample card --}}
                <div class="mt-4 rounded-lg border" style="background-color: var(--theme-surface, #fff);">
                    <div class="flex items-center gap-4 p-4">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background-color: {{ $this->primary }}; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; flex-shrink: 0;">T</div>
                        <div>
                            <p style="font-weight: 600; color: {{ $this->secondary }}; margin: 0;">{{ __('Card Title') }}</p>
                            <p style="font-size: 0.8rem; color: #666; margin: 0.125rem 0 0;">{{ __('Card description using the surface and text colours.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2 px-4 pb-4">
                        <span style="background-color: color-mix(in srgb, {{ $this->primary }} 20%, transparent); color: {{ $this->primary }}; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">{{ __('Tag one') }}</span>
                        <span style="background-color: color-mix(in srgb, {{ $this->secondary }} 20%, transparent); color: {{ $this->secondary }}; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">{{ __('Tag two') }}</span>
                    </div>
                </div>

                {{-- Sidebar sample --}}
                <div class="rounded-lg p-3" style="background-color: #f5f5f5;">
                    <div class="mb-2 flex items-center gap-2 rounded-md px-3 py-2" style="background-color: color-mix(in srgb, {{ $this->primary }} 12%, transparent); color: {{ $this->primary }}; font-weight: 500; font-size: 0.875rem;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $this->primary }}; display: inline-block;"></span>
                        {{ __('Active sidebar item') }}
                    </div>
                    <div style="font-size: 0.875rem; color: #666; padding: 0.5rem 0.75rem;">
                        {{ __('Inactive sidebar item') }}
                    </div>
                </div>

                {{-- Dark mode note --}}
                <div class="rounded-lg border p-3 text-xs" style="color: #888;">
                    <span style="font-weight: 600;">{{ __('Dark mode:') }}</span>
                    {{ __('Brand colours are unchanged in dark mode. Appearance only affects backgrounds and contrast — the accent stays the same.') }}
                </div>
            </div>
        </div>
    </div>
</section>
