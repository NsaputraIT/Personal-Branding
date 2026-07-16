<?php

use App\Models\Skill;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Skills settings')] class extends Component {

    /**
     * Skill list.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $skills = [];

    // Add / Edit form state
    public ?int $editSkillId = null;
    public ?int $deleteSkillId = null;
    public string $name = '';
    public string $description = '';
    public int $percentage = 0;
    public int $sortOrder = 0;
    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadSkills();
    }

    /**
     * Reload skills.
     */
    private function loadSkills(): void
    {
        $this->skills = Skill::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    /**
     * Open modal for adding skill.
     */
    public function addSkill(): void
    {
        $this->editSkillId = null;
        $this->name = '';
        $this->description = '';
        $this->percentage = 0;
        $this->sortOrder = 0;
        $this->isActive = true;

        $this->dispatch('modal-show', name: 'skill-form');
    }

    /**
     * Save skill.
     */
    public function saveSkill(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);


        if ($this->editSkillId) {

            $skill = Skill::findOrFail($this->editSkillId);

            $skill->update([
                'name' => $this->name,
                'description' => $this->description,
                'percentage' => $this->percentage,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);

        } else {

            Skill::create([
                'name' => $this->name,
                'description' => $this->description,
                'percentage' => $this->percentage,
                'sort_order' => $this->sortOrder,
                'is_active' => $this->isActive,
            ]);

        }


        $this->loadSkills();


        $this->resetForm();


        Flux::modal('skill-form')->close();


        Flux::toast(
            heading: 'Skill saved',
            text: 'Skill has been added successfully.',
            variant: 'success'
        );
    }

    /**
     * Reset skill form.
     */
    private function resetForm(): void
    {
        $this->editSkillId = null;
        $this->name = '';
        $this->description = '';
        $this->percentage = 0;
        $this->sortOrder = 0;
        $this->isActive = true;
    }

    /**
     * Open modal for editing skill.
     */
    public function editSkill(int $id): void
    {
        $skill = Skill::findOrFail($id);

        $this->editSkillId = $skill->id;
        $this->name = $skill->name;
        $this->description = $skill->description ?? '';
        $this->percentage = $skill->percentage;
        $this->sortOrder = $skill->sort_order;
        $this->isActive = $skill->is_active;

        Flux::modal('skill-form')->show();
    }

    /**
     * Open delete confirmation.
     */
    public function confirmDelete(int $id): void
    {
        $this->deleteSkillId = $id;

        Flux::modal('delete-skill-confirm')->show();
    }

    /**
     * Delete skill.
     */
    public function deleteSkill(): void
    {
        if (!$this->deleteSkillId) {
            return;
        }


        Skill::findOrFail($this->deleteSkillId)
            ->delete();


        $this->loadSkills();


        $this->deleteSkillId = null;


        Flux::modal('delete-skill-confirm')->close();


        Flux::toast(
            heading: 'Skill deleted',
            text: 'Skill has been removed successfully.',
            variant: 'success'
        );
    }

};

?>

<section class="w-full">

    <flux:heading size="xl" level="1">
        {{ __('Skills') }}
    </flux:heading>

    <flux:subheading size="lg" class="mb-6">
        {{ __('Manage the skills displayed on the portfolio website.') }}
    </flux:subheading>

    <flux:separator variant="subtle" />

    <div class="mt-8">

        <div class="mb-4 flex items-center justify-between">

            <div>

                <flux:heading level="3" size="lg">
                    {{ __('Skill List') }}
                </flux:heading>

                <flux:text class="mt-1">
                    {{ __('List of skills shown on your website.') }}
                </flux:text>

            </div>

            <flux:button
                variant="primary"
                wire:click="addSkill"
            >
                {{ __('Add Skill') }}
            </flux:button>

        </div>

        @if(count($skills) === 0)

            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">
                {{ __('No skills found.') }}
            </div>

        @else

            <div class="overflow-hidden rounded-xl border">

                <table class="w-full text-left text-sm">

                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">

                        <tr>

                            <th class="px-4 py-3">
                                {{ __('Skill') }}
                            </th>

                            <th class="px-4 py-3">
                                {{ __('Percentage') }}
                            </th>

                            <th class="px-4 py-3">
                                {{ __('Order') }}
                            </th>

                            <th class="px-4 py-3">
                                {{ __('Status') }}
                            </th>

                            <th class="px-4 py-3 text-right">
                                {{ __('Actions') }}
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($skills as $skill)

                            <tr class="border-b">

                                <td class="px-4 py-3">
                                    {{ $skill['name'] }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $skill['percentage'] }}%
                                </td>

                                <td class="px-4 py-3">
                                    {{ $skill['sort_order'] }}
                                </td>

                                <td class="px-4 py-3">

                                    @if($skill['is_active'])

                                        <span class="text-green-600">
                                            Active
                                        </span>

                                    @else

                                        <span class="text-red-600">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td class="px-4 py-3 text-right">

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        wire:click="editSkill({{ $skill['id'] }})"
                                        wire:loading.attr="disabled"
                                        icon="pencil-square"
                                        tooltip="Edit Skill"
                                    />

                                    <flux:button
                                        variant="danger"
                                        size="sm"
                                        wire:click="confirmDelete({{ $skill['id'] }})"
                                        wire:loading.attr="disabled"
                                        icon="trash"
                                        tooltip="Delete Skill"
                                    />

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

    <flux:modal name="skill-form" class="min-w-[28rem]">

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    {{ $editSkillId ? __('Edit Skill') : __('Add Skill') }}
                </flux:heading>

                <flux:text class="mt-1">
                    {{ __('Enter the skill information.') }}
                </flux:text>

            </div>

            <flux:field>

                <flux:label>
                    {{ __('Skill Name') }}
                </flux:label>

                <flux:input
                    wire:model="name"
                    type="text"
                />

            </flux:field>

            <flux:field>

                <flux:label>
                    {{ __('Description') }}
                </flux:label>

                <flux:textarea
                    wire:model="description"
                    rows="2"
                />

            </flux:field>

            <flux:field>

                <flux:label>
                    {{ __('Percentage') }}
                </flux:label>

                <flux:input
                    wire:model="percentage"
                    type="number"
                    min="0"
                    max="100"
                />

            </flux:field>

            <flux:field>

                <flux:label>
                    {{ __('Display Order') }}
                </flux:label>

                <flux:input
                    wire:model="sortOrder"
                    type="number"
                    min="0"
                />

            </flux:field>

            <flux:field>

                <label class="flex items-center gap-2">

                    <input
                        type="checkbox"
                        wire:model="isActive"
                    >

                    <span>
                        {{ __('Active') }}
                    </span>

                </label>

            </flux:field>

            <div class="flex gap-2">

                <flux:button
                    variant="primary"
                    wire:click="saveSkill"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>

                <flux:button
                    variant="ghost"
                    wire:click="$dispatch('modal-close', { name: 'skill-form' })"
                >
                    {{ __('Cancel') }}
                </flux:button>

            </div>

        </div>

    </flux:modal>

    <flux:modal name="delete-skill-confirm" class="min-w-[24rem]">

        <div class="space-y-6">

            <div>

                <flux:heading size="lg">
                    {{ __('Delete Skill') }}
                </flux:heading>

                <flux:text class="mt-1">
                    {{ __('Are you sure you want to delete this skill? This action cannot be undone.') }}
                </flux:text>

            </div>


            <div class="flex gap-2">

                <flux:button
                    variant="danger"
                    wire:click="deleteSkill"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>


                <flux:button
                    variant="ghost"
                    wire:click="$dispatch('modal-close', { name: 'delete-skill-confirm' })"
                >
                    {{ __('Cancel') }}
                </flux:button>

            </div>

        </div>

    </flux:modal>

</section>