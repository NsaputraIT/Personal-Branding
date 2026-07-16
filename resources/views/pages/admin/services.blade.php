<?php

use App\Models\Service;
use App\Models\Site;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Services')] class extends Component {
    public array $services = [];
    public string $sectionTitle = '';
    public string $sectionDescription = '';
    public string $sidebarHeading = '';
    public string $sidebarText = '';

    // Form state
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $icon = '';
    public string $title = '';
    public string $description = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadServices();
        $this->loadSectionMetadata();
    }

    private function loadServices(): void
    {
        $this->services = Service::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    private function loadSectionMetadata(): void
    {
        $meta = Site::firstOrFail()->section_metadata;
        $section = $meta['services'] ?? [];
        $this->sectionTitle = $section['title'] ?? '';
        $this->sectionDescription = $section['description'] ?? '';
        $this->sidebarHeading = $section['sidebar_heading'] ?? '';
        $this->sidebarText = $section['sidebar_text'] ?? '';
    }

    private function saveSectionMetadata(): void
    {
        $site = Site::firstOrFail();
        $meta = $site->section_metadata ?? [];
        $meta['services'] = [
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
            'sidebar_heading' => $this->sidebarHeading,
            'sidebar_text' => $this->sidebarText,
        ];
        $site->section_metadata = $meta;
        $site->save();
    }

    public function addService(): void
    {
        $this->editId = null;
        $this->icon = '';
        $this->title = '';
        $this->description = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->dispatch('modal-show', name: 'service-form');
    }

    public function saveService(): void
    {
        $this->validate([
            'icon' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        if ($this->editId) {
            Service::findOrFail($this->editId)->update([
                'icon' => $this->icon,
                'title' => $this->title,
                'description' => $this->description,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);
        } else {
            Service::create([
                'icon' => $this->icon,
                'title' => $this->title,
                'description' => $this->description,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);
        }

        $this->loadServices();
        $this->resetForm();
        Flux::modal('service-form')->close();
        Flux::toast(
            heading: $this->editId ? 'Service updated' : 'Service created',
            text: 'The service has been saved successfully.',
            variant: 'success'
        );
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->icon = '';
        $this->title = '';
        $this->description = '';
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    public function editService(int $id): void
    {
        $service = Service::findOrFail($id);
        $this->editId = $service->id;
        $this->icon = $service->icon;
        $this->title = $service->title;
        $this->description = $service->description;
        $this->sortOrder = $service->sort_order;
        $this->isActive = $service->is_active;
        Flux::modal('service-form')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        Flux::modal('delete-service-confirm')->show();
    }

    public function deleteService(): void
    {
        if (!$this->deleteId) return;
        Service::findOrFail($this->deleteId)->delete();
        $this->loadServices();
        $this->deleteId = null;
        Flux::modal('delete-service-confirm')->close();
        Flux::toast(
            heading: 'Service deleted',
            text: 'The service has been removed successfully.',
            variant: 'success'
        );
    }

    public function saveSection(): void
    {
        $this->validate([
            'sectionTitle' => ['nullable', 'string', 'max:255'],
            'sectionDescription' => ['nullable', 'string', 'max:65535'],
            'sidebarHeading' => ['nullable', 'string', 'max:255'],
            'sidebarText' => ['nullable', 'string', 'max:65535'],
        ]);

        $this->saveSectionMetadata();
        Flux::toast(
            heading: 'Section settings saved',
            text: 'The services section settings have been updated.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Services') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the services section and individual service cards.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- Section Metadata --}}
    <form wire:submit="saveSection" class="mt-8 max-w-2xl">
        <flux:heading level="3" size="lg">{{ __('Section Settings') }}</flux:heading>
        <flux:text class="mt-1 mb-4">{{ __('The section heading and description shown at the top.') }}</flux:text>

        <div class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Section Title') }}</flux:label>
                <flux:input wire:model="sectionTitle" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Section Description') }}</flux:label>
                <flux:textarea wire:model="sectionDescription" rows="2" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Sidebar Heading') }}</flux:label>
                <flux:input wire:model="sidebarHeading" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Sidebar Text') }}</flux:label>
                <flux:textarea wire:model="sidebarText" rows="3" />
            </flux:field>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save Section Settings') }}</span>
                <span wire:loading>{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>

    {{-- Service List --}}
    <div class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading level="3" size="lg">{{ __('Service Cards') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Individual service cards shown on the website.') }}</flux:text>
            </div>
            <flux:button variant="primary" wire:click="addService">{{ __('Add Service') }}</flux:button>
        </div>

        @if(count($services) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No services found.') }}</div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">{{ __('Icon') }}</th>
                            <th class="px-4 py-3">{{ __('Title') }}</th>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr class="border-b">
                                <td class="px-4 py-3"><i class="{{ $service['icon'] }}"></i> {{ $service['icon'] }}</td>
                                <td class="px-4 py-3">{{ $service['title'] }}</td>
                                <td class="px-4 py-3">{{ $service['sort_order'] }}</td>
                                <td class="px-4 py-3">
                                    @if($service['is_active'])
                                        <span class="text-green-600">Active</span>
                                    @else
                                        <span class="text-red-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button variant="ghost" size="sm" wire:click="editService({{ $service['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit Service" />
                                    <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $service['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete Service" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Service Form Modal --}}
    <flux:modal name="service-form" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editId ? __('Edit Service') : __('Add Service') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the service information.') }}</flux:text>
            </div>
            <flux:field>
                <flux:label>{{ __('Icon Class') }}</flux:label>
                <flux:input wire:model="icon" type="text" placeholder="bi-activity" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Title') }}</flux:label>
                <flux:input wire:model="title" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" />
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
                <flux:button variant="primary" wire:click="saveService" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'service-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-service-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Service') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure you want to delete this service? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="deleteService" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-service-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
