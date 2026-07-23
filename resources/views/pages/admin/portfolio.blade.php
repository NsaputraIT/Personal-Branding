<?php

use App\Models\PortfolioItem;
use App\Models\Site;
use App\Services\CdnUploadService;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Portfolio')] class extends Component {
    private CdnUploadService $cdn;

    public array $items = [];
    public string $sectionTitle = '';
    public string $sectionDescription = '';

    // Form state
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $category = '';
    public string $title = '';
    public string $description = '';
    public string $imagePath = ''; // CDN path
    public string $detailUrl = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public array $categories = ['web', 'graphics', 'motion', 'brand'];

    public function boot(CdnUploadService $cdn): void
    {
        $this->cdn = $cdn;
    }

    public function mount(): void
    {
        $this->loadItems();
        $this->loadSectionMetadata();
    }

    private function loadItems(): void
    {
        $this->items = PortfolioItem::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    private function loadSectionMetadata(): void
    {
        $meta = Site::firstOrFail()->section_metadata;
        $section = $meta['portfolio'] ?? [];
        $this->sectionTitle = $section['title'] ?? '';
        $this->sectionDescription = $section['description'] ?? '';
    }

    private function saveSectionMetadata(): void
    {
        $site = Site::firstOrFail();
        $meta = $site->section_metadata ?? [];
        $meta['portfolio'] = [
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ];
        $site->section_metadata = $meta;
        $site->save();
    }

    public function generatePresignedUrl(): array
    {
        return $this->cdn->deleteOldAndGeneratePresignedUrl('portfolio', $this->imagePath);
    }

    public function add(): void
    {
        $this->editId = null;
        $this->category = 'web';
        $this->title = '';
        $this->description = '';
        $this->imagePath = '';
        $this->detailUrl = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->dispatch('modal-show', name: 'portfolio-form');
    }

    public function save(): void
    {
        $this->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'imagePath' => ['required', 'string', 'max:500'],
            'detailUrl' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        $data = [
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $this->imagePath,
            'detail_url' => $this->detailUrl,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];

        if ($this->editId) {
            PortfolioItem::findOrFail($this->editId)->update($data);
        } else {
            PortfolioItem::create($data);
        }

        $this->loadItems();
        $this->resetForm();
        Flux::modal('portfolio-form')->close();
        Flux::toast(
            heading: $this->editId ? 'Portfolio item updated' : 'Portfolio item created',
            text: 'The portfolio item has been saved successfully.',
            variant: 'success'
        );
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->category = 'web';
        $this->title = '';
        $this->description = '';
        $this->imagePath = '';
        $this->detailUrl = '';
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    public function edit(int $id): void
    {
        $item = PortfolioItem::findOrFail($id);
        $this->editId = $item->id;
        $this->category = $item->category;
        $this->title = $item->title;
        $this->description = $item->description;
        $this->imagePath = $item->image_path;
        $this->detailUrl = $item->detail_url ?? '';
        $this->sortOrder = $item->sort_order;
        $this->isActive = $item->is_active;

        // Backward compatibility: convert legacy full URLs to relative paths
        if ($this->imagePath
            && (str_starts_with($this->imagePath, 'http://')
                || str_starts_with($this->imagePath, 'https://'))
        ) {
            $parsedPath = parse_url($this->imagePath, PHP_URL_PATH);
            $prefix = '/storage/';

            if ($parsedPath && str_starts_with($parsedPath, $prefix)) {
                $relative = ltrim(substr($parsedPath, strlen($prefix)), '/');

                if ($relative) {
                    $item->update(['image_path' => $relative]);
                    $this->imagePath = $relative;
                }
            }
        }

        Flux::modal('portfolio-form')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        Flux::modal('delete-portfolio-confirm')->show();
    }

    public function delete(): void
    {
        if (!$this->deleteId) return;

        $item = PortfolioItem::findOrFail($this->deleteId);

        // Delete associated image from CDN
        $this->cdn->delete($item->image_path);

        $item->delete();
        $this->loadItems();
        $this->deleteId = null;
        Flux::modal('delete-portfolio-confirm')->close();
        Flux::toast(
            heading: 'Portfolio item deleted',
            text: 'The portfolio item has been removed successfully.',
            variant: 'success'
        );
    }

    public function saveSection(): void
    {
        $this->validate([
            'sectionTitle' => ['nullable', 'string', 'max:255'],
            'sectionDescription' => ['nullable', 'string', 'max:65535'],
        ]);
        $this->saveSectionMetadata();
        Flux::toast(
            heading: 'Section settings saved',
            text: 'The portfolio section settings have been updated.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Portfolio') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the portfolio section and portfolio items.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="saveSection" class="mt-8 max-w-2xl">
        <flux:heading level="3" size="lg">{{ __('Section Settings') }}</flux:heading>
        <flux:text class="mt-1 mb-4">{{ __('The heading and description shown at the top of the portfolio section.') }}</flux:text>
        <div class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Section Title') }}</flux:label>
                <flux:input wire:model="sectionTitle" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Section Description') }}</flux:label>
                <flux:textarea wire:model="sectionDescription" rows="2" />
            </flux:field>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save Section Settings') }}</span>
                <span wire:loading>{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>

    <div class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading level="3" size="lg">{{ __('Portfolio Items') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Portfolio projects shown on the website.') }}</flux:text>
            </div>
            <flux:button variant="primary" wire:click="add">{{ __('Add Item') }}</flux:button>
        </div>

        @if(count($items) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No portfolio items found.') }}</div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">{{ __('Title') }}</th>
                            <th class="px-4 py-3">{{ __('Category') }}</th>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $item['title'] }}</td>
                                <td class="px-4 py-3"><span class="rounded bg-zinc-100 px-2 py-0.5 text-xs dark:bg-zinc-800">{{ $item['category'] }}</span></td>
                                <td class="px-4 py-3">{{ $item['sort_order'] }}</td>
                                <td class="px-4 py-3">
                                    @if($item['is_active'])
                                        <span class="text-green-600">Active</span>
                                    @else
                                        <span class="text-red-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button variant="ghost" size="sm" wire:click="edit({{ $item['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit" />
                                    <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $item['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <flux:modal name="portfolio-form" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editId ? __('Edit Portfolio Item') : __('Add Portfolio Item') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the portfolio item information.') }}</flux:text>
            </div>
            <flux:field>
                <flux:label>{{ __('Category') }}</flux:label>
                <select wire:model="category" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-600 dark:bg-zinc-800">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Title') }}</flux:label>
                <flux:input wire:model="title" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" />
            </flux:field>

            <div>
                <flux:label>{{ __('Image') }}</flux:label>
                <flux:text class="mt-1 mb-4">{{ __('Upload an image. It will be resized and converted to WebP automatically.') }}</flux:text>

                @php $cdnUrl = config('filesystems.disks.cdn.url'); @endphp

                <div class="flex items-center gap-6" x-data>
                    <div class="shrink-0">
                        <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                            <img src="{{ $imagePath && !str_starts_with($imagePath, 'asset/') ? $cdnUrl . '/' . ltrim($imagePath, '/') : asset($imagePath && str_starts_with($imagePath, 'asset/') ? $imagePath : 'asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}" id="portfolio-preview">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            x-ref="imageInput"
                            @change="
                                const file = $event.target.files[0];
                                if (!file) return;
                                const status = $el.closest('.flex').querySelector('.upload-status');
                                status.textContent = 'Processing...';
                                window.cdnUploader.fullPipeline(
                                    file, 'portfolio',
                                    () => $wire.generatePresignedUrl(),
                                    (path) => $wire.set('imagePath', path)
                                ).then((path) => {
                                    status.textContent = 'Upload complete';
                                    document.getElementById('portfolio-preview').src = '{{ $cdnUrl }}/' + (path.charAt(0) === '/' ? path.slice(1) : path);
                                }).catch((err) => {
                                    status.textContent = 'Upload failed: ' + err.message;
                                });
                            "
                        />

                        <div class="flex items-center gap-2">
                            <flux:button type="button" variant="primary" x-on:click="$refs.imageInput.click()">
                                {{ __('Upload Image') }}
                            </flux:button>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            <span class="upload-status">
                                @if ($imagePath && !str_starts_with($imagePath, 'asset/'))
                                    {{ __('CDN image loaded') }}
                                @elseif ($imagePath)
                                    {{ __('Current image loaded') }}
                                @else
                                    {{ __('No file selected') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <flux:error wire:model="imagePath" class="mt-2" />
            </div>

            <flux:field>
                <flux:label>{{ __('Detail URL (optional)') }}</flux:label>
                <flux:input wire:model="detailUrl" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Display Order') }}</flux:label>
                <flux:input wire:model="sortOrder" type="number" min="0" />
            </flux:field>
            <flux:field>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isActive">
                    <span>{{ __('Active') }}</span>
                </label>
            </flux:field>
            <div class="flex gap-2">
                <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'portfolio-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-portfolio-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Portfolio Item') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-portfolio-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
