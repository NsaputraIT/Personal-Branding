<?php

use App\Models\ResumeEntry;
use App\Models\Site;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Resume')] class extends Component {
    public array $workEntries = [];
    public array $educationEntries = [];
    public string $sectionTitle = '';
    public string $sectionDescription = '';
    public string $activeTab = 'work';

    // Form state
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $entryType = 'work';
    public string $institution = '';
    public string $position = '';
    public string $periodStart = '';
    public string $periodEnd = '';
    public string $description = '';
    public string $bulletPoints = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadEntries();
        $this->loadSectionMetadata();
    }

    private function loadEntries(): void
    {
        $this->workEntries = ResumeEntry::where('type', 'work')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();

        $this->educationEntries = ResumeEntry::where('type', 'education')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    private function loadSectionMetadata(): void
    {
        $meta = Site::firstOrFail()->section_metadata;
        $section = $meta['resume'] ?? [];
        $this->sectionTitle = $section['title'] ?? '';
        $this->sectionDescription = $section['description'] ?? '';
    }

    private function saveSectionMetadata(): void
    {
        $site = Site::firstOrFail();
        $meta = $site->section_metadata ?? [];
        $meta['resume'] = [
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ];
        $site->section_metadata = $meta;
        $site->save();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function add(string $type): void
    {
        $this->editId = null;
        $this->entryType = $type;
        $this->institution = '';
        $this->position = '';
        $this->periodStart = '';
        $this->periodEnd = '';
        $this->description = '';
        $this->bulletPoints = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->dispatch('modal-show', name: 'resume-form');
    }

    public function save(): void
    {
        $this->validate([
            'entryType' => ['required', 'in:work,education'],
            'institution' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'periodStart' => ['nullable', 'string', 'max:100'],
            'periodEnd' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'bulletPoints' => ['nullable', 'string'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        $data = [
            'type' => $this->entryType,
            'institution' => $this->institution,
            'position' => $this->position,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'description' => $this->description,
            'bullet_points' => $this->bulletPoints
                ? array_filter(array_map('trim', explode("\n", str_replace("\r\n", "\n", $this->bulletPoints))))
                : [],
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];

        if ($this->editId) {
            ResumeEntry::findOrFail($this->editId)->update($data);
        } else {
            ResumeEntry::create($data);
        }

        $this->loadEntries();
        $this->resetForm();
        Flux::modal('resume-form')->close();
        Flux::toast(
            heading: $this->editId ? 'Entry updated' : 'Entry created',
            text: 'The resume entry has been saved successfully.',
            variant: 'success'
        );
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->institution = '';
        $this->position = '';
        $this->periodStart = '';
        $this->periodEnd = '';
        $this->description = '';
        $this->bulletPoints = '';
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    public function edit(int $id): void
    {
        $item = ResumeEntry::findOrFail($id);
        $this->editId = $item->id;
        $this->entryType = $item->type;
        $this->institution = $item->institution;
        $this->position = $item->position;
        $this->periodStart = $item->period_start ?? '';
        $this->periodEnd = $item->period_end ?? '';
        $this->description = $item->description ?? '';
        $this->bulletPoints = implode("\n", $item->bullet_points ?? []);
        $this->sortOrder = $item->sort_order;
        $this->isActive = $item->is_active;
        $this->activeTab = $item->type === 'work' ? 'work' : 'education';
        Flux::modal('resume-form')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        Flux::modal('delete-resume-confirm')->show();
    }

    public function delete(): void
    {
        if (!$this->deleteId) return;
        ResumeEntry::findOrFail($this->deleteId)->delete();
        $this->loadEntries();
        $this->deleteId = null;
        Flux::modal('delete-resume-confirm')->close();
        Flux::toast(
            heading: 'Entry deleted',
            text: 'The resume entry has been removed successfully.',
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
            text: 'The resume section settings have been updated.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Resume') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage work experience and education entries.') }}</flux:subheading>
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

    {{-- Tabs --}}
    <div class="mt-8">
        <div class="mb-4 border-b">
            <div class="flex gap-4">
                <button wire:click="switchTab('work')" class="border-b-2 px-4 py-2 text-sm font-medium {{ $activeTab === 'work' ? 'border-blue-600 text-blue-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Work Experience') }}
                </button>
                <button wire:click="switchTab('education')" class="border-b-2 px-4 py-2 text-sm font-medium {{ $activeTab === 'education' ? 'border-blue-600 text-blue-600' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                    {{ __('Education') }}
                </button>
            </div>
        </div>

        @if($activeTab === 'work')
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <flux:heading level="3" size="lg">{{ __('Work Experience') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Job and work history entries.') }}</flux:text>
                </div>
                <flux:button variant="primary" wire:click="add('work')">{{ __('Add Experience') }}</flux:button>
            </div>

            @if(count($workEntries) === 0)
                <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No work entries found.') }}</div>
            @else
                <div class="overflow-hidden rounded-xl border">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-3">{{ __('Company') }}</th>
                                <th class="px-4 py-3">{{ __('Position') }}</th>
                                <th class="px-4 py-3">{{ __('Period') }}</th>
                                <th class="px-4 py-3">{{ __('Order') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workEntries as $entry)
                                <tr class="border-b">
                                    <td class="px-4 py-3">{{ $entry['institution'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['position'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['period_start'] }} - {{ $entry['period_end'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['sort_order'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($entry['is_active'])
                                            <span class="text-green-600">Active</span>
                                        @else
                                            <span class="text-red-600">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:button variant="ghost" size="sm" wire:click="edit({{ $entry['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit" />
                                        <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $entry['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif

        @if($activeTab === 'education')
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <flux:heading level="3" size="lg">{{ __('Education') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Educational background entries.') }}</flux:text>
                </div>
                <flux:button variant="primary" wire:click="add('education')">{{ __('Add Education') }}</flux:button>
            </div>

            @if(count($educationEntries) === 0)
                <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No education entries found.') }}</div>
            @else
                <div class="overflow-hidden rounded-xl border">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-3">{{ __('Institution') }}</th>
                                <th class="px-4 py-3">{{ __('Degree') }}</th>
                                <th class="px-4 py-3">{{ __('Period') }}</th>
                                <th class="px-4 py-3">{{ __('Order') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($educationEntries as $entry)
                                <tr class="border-b">
                                    <td class="px-4 py-3">{{ $entry['institution'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['position'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['period_start'] }} - {{ $entry['period_end'] }}</td>
                                    <td class="px-4 py-3">{{ $entry['sort_order'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($entry['is_active'])
                                            <span class="text-green-600">Active</span>
                                        @else
                                            <span class="text-red-600">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:button variant="ghost" size="sm" wire:click="edit({{ $entry['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit" />
                                        <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $entry['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>

    <flux:modal name="resume-form" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editId ? __('Edit Entry') : __('Add Entry') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the resume information.') }}</flux:text>
            </div>
            <input type="hidden" wire:model="entryType" />
            <flux:field>
                <flux:label>{{ __('Institution / Company') }}</flux:label>
                <flux:input wire:model="institution" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ $entryType === 'work' ? __('Position') : __('Degree') }}</flux:label>
                <flux:input wire:model="position" type="text" />
            </flux:field>
            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Period Start') }}</flux:label>
                    <flux:input wire:model="periodStart" type="text" placeholder="Jun, 2023" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Period End') }}</flux:label>
                    <flux:input wire:model="periodEnd" type="text" placeholder="Current" />
                </flux:field>
            </div>
            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" />
            </flux:field>
            @if($entryType === 'work')
                <flux:field>
                    <flux:label>{{ __('Bullet Points') }}</flux:label>
                    <flux:textarea wire:model="bulletPoints" rows="4" />
                    <flux:error class="mt-1 text-xs text-zinc-400">{{ __('One item per line.') }}</flux:error>
                </flux:field>
            @endif
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
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'resume-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-resume-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Entry') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-resume-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
