<?php

use App\Models\Testimonial;
use App\Models\Site;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Title('Testimonials')] class extends Component {
    use WithFileUploads;

    public array $testimonials = [];
    public string $sectionTitle = '';
    public string $sectionDescription = '';

    // Form state
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $name = '';
    public string $role = '';
    public string $quoteHeading = '';
    public string $quoteParagraphs = '';
    public string $avatarPath = '';
    public $avatarFile = null;
    public string $featuredImagePath = '';
    public $featuredImageFile = null;
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadTestimonials();
        $this->loadSectionMetadata();
    }

    private function loadTestimonials(): void
    {
        $this->testimonials = Testimonial::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    private function loadSectionMetadata(): void
    {
        $meta = Site::firstOrFail()->section_metadata;
        $section = $meta['testimonials'] ?? [];
        $this->sectionTitle = $section['title'] ?? '';
        $this->sectionDescription = $section['description'] ?? '';
    }

    private function saveSectionMetadata(): void
    {
        $site = Site::firstOrFail();
        $meta = $site->section_metadata ?? [];
        $meta['testimonials'] = [
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ];
        $site->section_metadata = $meta;
        $site->save();
    }

    public function add(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->role = '';
        $this->quoteHeading = '';
        $this->quoteParagraphs = '';
        $this->avatarPath = '';
        $this->avatarFile = null;
        $this->featuredImagePath = '';
        $this->featuredImageFile = null;
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->dispatch('modal-show', name: 'testimonial-form');
    }

    public function removeAvatar(): void
    {
        $this->avatarFile = null;
    }

    public function removeFeaturedImage(): void
    {
        $this->featuredImageFile = null;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'quoteHeading' => ['nullable', 'string', 'max:255'],
            'quoteParagraphs' => ['required', 'string'],
            'avatarPath' => ['required_without:avatarFile', 'string', 'max:255'],
            'avatarFile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'featuredImagePath' => ['nullable', 'string', 'max:255'],
            'featuredImageFile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        if ($this->avatarFile) {
            $path = $this->avatarFile->store('testimonials', 'public');

            if ($this->editId && $this->avatarPath && !str_contains($this->avatarPath, '..')) {
                Storage::disk('public')->delete($this->avatarPath);
            }

            $this->avatarPath = $path;
        }

        if ($this->featuredImageFile) {
            $path = $this->featuredImageFile->store('testimonials', 'public');

            if ($this->editId && $this->featuredImagePath && !str_contains($this->featuredImagePath, '..')) {
                Storage::disk('public')->delete($this->featuredImagePath);
            }

            $this->featuredImagePath = $path;
        }

        $data = [
            'name' => $this->name,
            'role' => $this->role,
            'quote_heading' => $this->quoteHeading,
            'quote_paragraphs' => array_filter(explode("\n\n", str_replace("\r\n", "\n", $this->quoteParagraphs))),
            'avatar_path' => $this->avatarPath,
            'featured_image_path' => $this->featuredImagePath,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];

        if ($this->editId) {
            Testimonial::findOrFail($this->editId)->update($data);
        } else {
            Testimonial::create($data);
        }

        $this->loadTestimonials();
        $this->resetForm();
        Flux::modal('testimonial-form')->close();
        Flux::toast(
            heading: $this->editId ? 'Testimonial updated' : 'Testimonial created',
            text: 'The testimonial has been saved successfully.',
            variant: 'success'
        );
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->role = '';
        $this->quoteHeading = '';
        $this->quoteParagraphs = '';
        $this->avatarPath = '';
        $this->avatarFile = null;
        $this->featuredImagePath = '';
        $this->featuredImageFile = null;
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    public function edit(int $id): void
    {
        $item = Testimonial::findOrFail($id);
        $this->editId = $item->id;
        $this->name = $item->name;
        $this->role = $item->role;
        $this->quoteHeading = $item->quote_heading ?? '';
        $this->quoteParagraphs = implode("\n\n", $item->quote_paragraphs ?? []);
        $this->avatarPath = $item->avatar_path;
        $this->featuredImagePath = $item->featured_image_path ?? '';
        $this->sortOrder = $item->sort_order;
        $this->isActive = $item->is_active;

        // Backward compatibility: convert legacy full URLs to relative paths
        foreach (['avatarPath' => 'avatar_path', 'featuredImagePath' => 'featured_image_path'] as $prop => $column) {
            $value = $this->{$prop};
            if ($value
                && (str_starts_with($value, 'http://')
                    || str_starts_with($value, 'https://'))
            ) {
                $parsedPath = parse_url($value, PHP_URL_PATH);
                $prefix = '/storage/';

                if ($parsedPath && str_starts_with($parsedPath, $prefix)) {
                    $relative = ltrim(substr($parsedPath, strlen($prefix)), '/');

                    if ($relative) {
                        $item->update([$column => $relative]);
                        $this->{$prop} = $relative;
                    }
                }
            }
        }

        Flux::modal('testimonial-form')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        Flux::modal('delete-testimonial-confirm')->show();
    }

    public function delete(): void
    {
        if (!$this->deleteId) return;

        $item = Testimonial::findOrFail($this->deleteId);

        // Delete associated images
        if ($item->avatar_path && !str_contains($item->avatar_path, '..')) {
            Storage::disk('public')->delete($item->avatar_path);
        }
        if ($item->featured_image_path && !str_contains($item->featured_image_path, '..')) {
            Storage::disk('public')->delete($item->featured_image_path);
        }

        $item->delete();
        $this->loadTestimonials();
        $this->deleteId = null;
        Flux::modal('delete-testimonial-confirm')->close();
        Flux::toast(
            heading: 'Testimonial deleted',
            text: 'The testimonial has been removed successfully.',
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
            text: 'The testimonials section settings have been updated.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Testimonials') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage testimonials and the section settings.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    <form wire:submit="saveSection" class="mt-8 max-w-2xl">
        <flux:heading level="3" size="lg">{{ __('Section Settings') }}</flux:heading>
        <flux:text class="mt-1 mb-4">{{ __('The heading and description shown at the top.') }}</flux:text>
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
                <flux:heading level="3" size="lg">{{ __('Testimonial List') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Client testimonials shown on the website.') }}</flux:text>
            </div>
            <flux:button variant="primary" wire:click="add">{{ __('Add Testimonial') }}</flux:button>
        </div>

        @if(count($testimonials) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No testimonials found.') }}</div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Role') }}</th>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $t)
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $t['name'] }}</td>
                                <td class="px-4 py-3">{{ $t['role'] }}</td>
                                <td class="px-4 py-3">{{ $t['sort_order'] }}</td>
                                <td class="px-4 py-3">
                                    @if($t['is_active'])
                                        <span class="text-green-600">Active</span>
                                    @else
                                        <span class="text-red-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button variant="ghost" size="sm" wire:click="edit({{ $t['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit" />
                                    <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $t['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <flux:modal name="testimonial-form" class="min-w-[32rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editId ? __('Edit Testimonial') : __('Add Testimonial') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the testimonial information.') }}</flux:text>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="name" type="text" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Role') }}</flux:label>
                    <flux:input wire:model="role" type="text" />
                </flux:field>
            </div>
            <flux:field>
                <flux:label>{{ __('Quote Heading') }}</flux:label>
                <flux:input wire:model="quoteHeading" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Quote Paragraphs') }}</flux:label>
                <flux:textarea wire:model="quoteParagraphs" rows="4" />
                <flux:error class="mt-1 text-xs text-zinc-400">{{ __('Separate paragraphs with a blank line.') }}</flux:error>
            </flux:field>

            <div>
                <flux:label>{{ __('Avatar Image') }}</flux:label>
                <flux:text class="mt-1 mb-4">{{ __('Upload an avatar image.') }}</flux:text>

                <div class="flex items-center gap-6">
                    <div class="shrink-0">
                        <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                            @if ($avatarFile)
                                <img src="{{ $avatarFile->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}">
                            @elseif ($avatarPath && !str_starts_with($avatarPath, 'asset/'))
                                <img src="{{ Storage::url($avatarPath) }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Current avatar') }}">
                            @else
                                <img src="{{ asset('asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('No image selected') }}">
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input
                            type="file"
                            wire:model="avatarFile"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="hidden"
                            x-ref="avatarInput"
                        />

                        <div class="flex items-center gap-2">
                            <flux:button type="button" variant="primary" x-on:click="$refs.avatarInput.click()">
                                {{ __('Upload Image') }}
                            </flux:button>

                            @if ($avatarFile)
                                <flux:button type="button" variant="ghost" wire:click="removeAvatar" wire:loading.attr="disabled">
                                    {{ __('Remove Image') }}
                                </flux:button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            @if ($avatarFile)
                                <span>{{ $avatarFile->getClientOriginalName() }}</span>
                            @elseif ($avatarPath)
                                <span>{{ __('Current image loaded') }}</span>
                            @else
                                <span>{{ __('No file selected') }}</span>
                            @endif
                            <span wire:loading wire:target="avatarFile">{{ __('Uploading...') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <flux:label>{{ __('Featured Image') }}</flux:label>
                <flux:text class="mt-1 mb-4">{{ __('Upload a featured image (optional).') }}</flux:text>

                <div class="flex items-center gap-6">
                    <div class="shrink-0">
                        <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                            @if ($featuredImageFile)
                                <img src="{{ $featuredImageFile->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}">
                            @elseif ($featuredImagePath && !str_starts_with($featuredImagePath, 'asset/'))
                                <img src="{{ Storage::url($featuredImagePath) }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Current featured image') }}">
                            @else
                                <img src="{{ asset('asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('No image selected') }}">
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input
                            type="file"
                            wire:model="featuredImageFile"
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="hidden"
                            x-ref="featuredImageInput"
                        />

                        <div class="flex items-center gap-2">
                            <flux:button type="button" variant="primary" x-on:click="$refs.featuredImageInput.click()">
                                {{ __('Upload Image') }}
                            </flux:button>

                            @if ($featuredImageFile)
                                <flux:button type="button" variant="ghost" wire:click="removeFeaturedImage" wire:loading.attr="disabled">
                                    {{ __('Remove Image') }}
                                </flux:button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            @if ($featuredImageFile)
                                <span>{{ $featuredImageFile->getClientOriginalName() }}</span>
                            @elseif ($featuredImagePath)
                                <span>{{ __('Current image loaded') }}</span>
                            @else
                                <span>{{ __('No file selected') }}</span>
                            @endif
                            <span wire:loading wire:target="featuredImageFile">{{ __('Uploading...') }}</span>
                        </div>
                    </div>
                </div>
            </div>

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
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'testimonial-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-testimonial-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Testimonial') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-testimonial-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
