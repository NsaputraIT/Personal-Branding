<?php

use App\Models\FaqItem;
use App\Models\Site;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('FAQ')] class extends Component {
    public array $faqItems = [];
    public string $sectionTitle = '';
    public string $sectionDescription = '';

    // Form state
    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $question = '';
    public string $answer = '';
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadFaqItems();
        $this->loadSectionMetadata();
    }

    private function loadFaqItems(): void
    {
        $this->faqItems = FaqItem::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    private function loadSectionMetadata(): void
    {
        $meta = Site::firstOrFail()->section_metadata;
        $section = $meta['faq'] ?? [];
        $this->sectionTitle = $section['title'] ?? '';
        $this->sectionDescription = $section['description'] ?? '';
    }

    private function saveSectionMetadata(): void
    {
        $site = Site::firstOrFail();
        $meta = $site->section_metadata ?? [];
        $meta['faq'] = [
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ];
        $site->section_metadata = $meta;
        $site->save();
    }

    public function add(): void
    {
        $this->editId = null;
        $this->question = '';
        $this->answer = '';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->dispatch('modal-show', name: 'faq-form');
    }

    public function save(): void
    {
        $this->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        if ($this->editId) {
            FaqItem::findOrFail($this->editId)->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);
        } else {
            FaqItem::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);
        }

        $this->loadFaqItems();
        $this->resetForm();
        Flux::modal('faq-form')->close();
        Flux::toast(
            heading: $this->editId ? 'FAQ updated' : 'FAQ created',
            text: 'The FAQ item has been saved successfully.',
            variant: 'success'
        );
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->question = '';
        $this->answer = '';
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    public function edit(int $id): void
    {
        $item = FaqItem::findOrFail($id);
        $this->editId = $item->id;
        $this->question = $item->question;
        $this->answer = $item->answer;
        $this->sortOrder = $item->sort_order;
        $this->isActive = $item->is_active;
        Flux::modal('faq-form')->show();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        Flux::modal('delete-faq-confirm')->show();
    }

    public function delete(): void
    {
        if (!$this->deleteId) return;
        FaqItem::findOrFail($this->deleteId)->delete();
        $this->loadFaqItems();
        $this->deleteId = null;
        Flux::modal('delete-faq-confirm')->close();
        Flux::toast(
            heading: 'FAQ deleted',
            text: 'The FAQ item has been removed successfully.',
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
            text: 'The FAQ section settings have been updated.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('FAQ') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage frequently asked questions.') }}</flux:subheading>
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
                <flux:heading level="3" size="lg">{{ __('FAQ Items') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Questions and answers shown on the website.') }}</flux:text>
            </div>
            <flux:button variant="primary" wire:click="add">{{ __('Add FAQ') }}</flux:button>
        </div>

        @if(count($faqItems) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No FAQ items found.') }}</div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">{{ __('Question') }}</th>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqItems as $item)
                            <tr class="border-b">
                                <td class="max-w-xs truncate px-4 py-3">{{ $item['question'] }}</td>
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

    <flux:modal name="faq-form" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editId ? __('Edit FAQ') : __('Add FAQ') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the question and answer.') }}</flux:text>
            </div>
            <flux:field>
                <flux:label>{{ __('Question') }}</flux:label>
                <flux:input wire:model="question" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Answer') }}</flux:label>
                <flux:textarea wire:model="answer" rows="4" />
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
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'faq-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-faq-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete FAQ') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-faq-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
