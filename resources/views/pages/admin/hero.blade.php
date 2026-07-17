<?php

use App\Models\HeroSection;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Title('Hero Section')] class extends Component {
    use WithFileUploads;

    public string $heading = '';
    public string $subheading = '';
    public string $ctaPrimaryText = '';
    public string $ctaPrimaryUrl = '';
    public string $ctaSecondaryText = '';
    public string $ctaSecondaryUrl = '';
    public string $profileImage = '';
    public $profileImageFile = null;
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

        // Backward compatibility: convert legacy full URLs to relative paths
        if ($this->profileImage
            && (str_starts_with($this->profileImage, 'http://')
                || str_starts_with($this->profileImage, 'https://'))
        ) {
            $parsedPath = parse_url($this->profileImage, PHP_URL_PATH);
            $prefix = '/storage/';

            if ($parsedPath && str_starts_with($parsedPath, $prefix)) {
                $relative = ltrim(substr($parsedPath, strlen($prefix)), '/');

                if ($relative) {
                    HeroSection::firstOrFail()->update(['profile_image' => $relative]);
                    $this->profileImage = $relative;
                }
            }
        }
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

    public function removeImage(): void
    {
        $this->profileImageFile = null;
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
            'profileImageFile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'stats' => ['nullable', 'array'],
        ]);

        if ($this->profileImageFile) {
            $path = $this->profileImageFile->store('hero', 'public');

            if ($this->profileImage) {
                Storage::disk('public')->delete($this->profileImage);
            }

            $this->profileImage = $path;
        }

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

        $this->profileImageFile = null;

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
            <flux:text class="mt-1 mb-4">{{ __('Upload a profile image for the hero section.') }}</flux:text>

            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                        @if ($profileImageFile)
                            <img src="{{ $profileImageFile->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}">
                        @elseif ($profileImage)
                            <img src="{{ Storage::url($profileImage) }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Current profile image') }}">
                        @else
                            <img src="{{ asset('asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('No image selected') }}">
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <input
                        type="file"
                        wire:model="profileImageFile"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="hidden"
                        x-ref="profileImageInput"
                    />

                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="primary" x-on:click="$refs.profileImageInput.click()">
                            {{ __('Upload Image') }}
                        </flux:button>

                        @if ($profileImageFile)
                            <flux:button type="button" variant="ghost" wire:click="removeImage" wire:loading.attr="disabled">
                                {{ __('Remove Image') }}
                            </flux:button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        @if ($profileImageFile)
                            <span>{{ $profileImageFile->getClientOriginalName() }}</span>
                        @elseif ($profileImage)
                            <span>{{ __('Current image loaded') }}</span>
                        @else
                            <span>{{ __('No file selected') }}</span>
                        @endif
                        <span wire:loading wire:target="profileImageFile">{{ __('Uploading...') }}</span>
                    </div>
                </div>
            </div>
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
