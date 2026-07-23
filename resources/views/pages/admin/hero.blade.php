<?php

use App\Models\HeroSection;
use App\Services\CdnUploadService;
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
    public string $cdnPath = ''; // CDN path, e.g. /hero/timestamp_random.webp
    public array $stats = [];

    private CdnUploadService $cdn;

    public function boot(CdnUploadService $cdn): void
    {
        $this->cdn = $cdn;
    }

    public function mount(): void
    {
        $hero = HeroSection::firstOrFail();
        $this->heading = $hero->heading ?? '';
        $this->subheading = $hero->subheading ?? '';
        $this->ctaPrimaryText = $hero->cta_primary_text ?? '';
        $this->ctaPrimaryUrl = $hero->cta_primary_url ?? '';
        $this->ctaSecondaryText = $hero->cta_secondary_text ?? '';
        $this->ctaSecondaryUrl = $hero->cta_secondary_url ?? '';
        $this->cdnPath = $hero->profile_image ?? '';
        $this->stats = $hero->stats ?? [];

        // Backward compatibility: convert legacy full URLs to relative paths
        if ($this->cdnPath
            && (str_starts_with($this->cdnPath, 'http://')
                || str_starts_with($this->cdnPath, 'https://'))
        ) {
            $parsedPath = parse_url($this->cdnPath, PHP_URL_PATH);
            $prefix = '/storage/';

            if ($parsedPath && str_starts_with($parsedPath, $prefix)) {
                $relative = ltrim(substr($parsedPath, strlen($prefix)), '/');

                if ($relative) {
                    HeroSection::firstOrFail()->update(['profile_image' => $relative]);
                    $this->cdnPath = $relative;
                }
            }
        }
    }

    public function generatePresignedUrl(string $directory): array
    {
        return $this->cdn->deleteOldAndGeneratePresignedUrl($directory, $this->cdnPath);
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
            'cdnPath' => ['nullable', 'string', 'max:500'],
            'stats' => ['nullable', 'array'],
        ]);

        HeroSection::firstOrFail()->update([
            'heading' => $this->heading,
            'subheading' => $this->subheading,
            'cta_primary_text' => $this->ctaPrimaryText,
            'cta_primary_url' => $this->ctaPrimaryUrl,
            'cta_secondary_text' => $this->ctaSecondaryText,
            'cta_secondary_url' => $this->ctaSecondaryUrl,
            'profile_image' => $this->cdnPath,
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
            <flux:text class="mt-1 mb-4">{{ __('Upload a profile image for the hero section. Image will be resized and converted to WebP automatically.') }}</flux:text>

            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                        @php $cdnUrl = config('filesystems.disks.cdn.url'); @endphp
                        <img src="{{ $cdnPath && !str_starts_with($cdnPath, 'asset/') ? $cdnUrl . '/' . ltrim($cdnPath, '/') : asset($cdnPath && str_starts_with($cdnPath, 'asset/') ? $cdnPath : 'asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ $cdnPath ? __('Current profile image') : __('No image selected') }}" id="hero-preview">
                    </div>
                </div>

                <div class="flex flex-col gap-2" x-data>
                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        x-ref="fileInput"
                        @change="
                            const file = $event.target.files[0];
                            if (!file) return;
                            const btn = $el.closest('.flex').querySelector('.upload-btn');
                            const status = $el.closest('.flex').querySelector('.upload-status');
                            btn.disabled = true;
                            status.textContent = 'Processing...';
                            window.cdnUploader.fullPipeline(
                                file,
                                'hero',
                                (dir) => $wire.generatePresignedUrl(dir),
                                (path) => $wire.set('cdnPath', path),
                            ).then((path) => {
                                status.textContent = 'Upload complete';
                                btn.disabled = false;
                                // Refresh preview
                                const cdnUrl = '{{ $cdnUrl }}';
                                document.getElementById('hero-preview').src = cdnUrl + '/' + (path.charAt(0) === '/' ? path.slice(1) : path);
                            }).catch((err) => {
                                status.textContent = 'Upload failed: ' + err.message;
                                btn.disabled = false;
                            });
                        "
                    />

                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="primary" class="upload-btn" x-on:click="$refs.fileInput.click()">
                            {{ __('Upload Image') }}
                        </flux:button>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span class="upload-status">
                            @if ($cdnPath && !str_starts_with($cdnPath, 'asset/'))
                                {{ __('CDN image loaded') }}
                            @elseif ($cdnPath)
                                {{ __('Current image loaded') }}
                            @else
                                {{ __('No file selected') }}
                            @endif
                        </span>
                        <span wire:loading wire:target="generatePresignedUrl">{{ __('Processing...') }}</span>
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
