<?php

use App\Models\AboutSection;
use App\Models\Skill;
use App\Services\CdnUploadService;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('About')] class extends Component {
    private CdnUploadService $cdn;

    // --- About section fields ---
    public string $heading = '';
    public string $description = '';
    public string $subtitle = '';
    public string $paragraph1 = '';
    public string $paragraph2 = '';
    public string $profileImagePath = ''; // CDN path
    public string $signatureImagePath = ''; // CDN path
    public string $signatureName = '';
    public string $signatureTitle = '';
    public array $infoItems = [];

    // --- Skills CRUD fields ---
    public array $skills = [];
    public ?int $editSkillId = null;
    public ?int $deleteSkillId = null;
    public string $skillName = '';
    public string $skillDescription = '';
    public int $skillPercentage = 0;
    public int $skillSortOrder = 0;
    public bool $skillIsActive = true;

    public function boot(CdnUploadService $cdn): void
    {
        $this->cdn = $cdn;
    }

    public function mount(): void
    {
        $about = AboutSection::firstOrFail();
        $this->heading = $about->heading ?? '';
        $this->description = $about->description ?? '';
        $this->subtitle = $about->subtitle ?? '';
        $this->paragraph1 = $about->paragraph1 ?? '';
        $this->paragraph2 = $about->paragraph2 ?? '';
        $this->profileImagePath = $about->profile_image ?? '';
        $this->signatureImagePath = $about->signature_image ?? '';
        $this->signatureName = $about->signature_name ?? '';
        $this->signatureTitle = $about->signature_title ?? '';
        $this->infoItems = $about->info_items ?? [];

        // Backward compatibility: convert legacy full URLs to relative paths
        foreach (['profileImagePath' => 'profile_image', 'signatureImagePath' => 'signature_image'] as $prop => $column) {
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
                        AboutSection::firstOrFail()->update([$column => $relative]);
                        $this->{$prop} = $relative;
                    }
                }
            }
        }

        $this->loadSkills();
    }

    // ==================== ABOUT METHODS ====================

    public function generatePresignedUrl(string $field): array
    {
        $directory = 'about';

        if ($field === 'profileImagePath') {
            return $this->cdn->deleteOldAndGeneratePresignedUrl($directory, $this->profileImagePath);
        }

        if ($field === 'signatureImagePath') {
            return $this->cdn->deleteOldAndGeneratePresignedUrl($directory, $this->signatureImagePath);
        }

        return $this->cdn->generatePresignedUrl($directory);
    }

    public function addInfoItem(): void
    {
        $this->infoItems[] = ['label' => '', 'value' => ''];
    }

    public function removeInfoItem(int $index): void
    {
        unset($this->infoItems[$index]);
        $this->infoItems = array_values($this->infoItems);
    }

    public function save(): void
    {
        $this->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'paragraph1' => ['nullable', 'string', 'max:65535'],
            'paragraph2' => ['nullable', 'string', 'max:65535'],
            'profileImagePath' => ['nullable', 'string', 'max:500'],
            'signatureImagePath' => ['nullable', 'string', 'max:500'],
            'signatureName' => ['nullable', 'string', 'max:255'],
            'signatureTitle' => ['nullable', 'string', 'max:255'],
            'infoItems' => ['nullable', 'array'],
        ]);

        AboutSection::firstOrFail()->update([
            'heading' => $this->heading,
            'description' => $this->description,
            'subtitle' => $this->subtitle,
            'paragraph1' => $this->paragraph1,
            'paragraph2' => $this->paragraph2,
            'profile_image' => $this->profileImagePath,
            'signature_image' => $this->signatureImagePath,
            'signature_name' => $this->signatureName,
            'signature_title' => $this->signatureTitle,
            'info_items' => $this->infoItems,
        ]);

        Flux::toast(
            heading: 'About section saved',
            text: 'The about section has been updated successfully.',
            variant: 'success'
        );
    }

    // ==================== SKILL METHODS ====================

    private function loadSkills(): void
    {
        $this->skills = Skill::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function addSkill(): void
    {
        $this->editSkillId = null;
        $this->skillName = '';
        $this->skillDescription = '';
        $this->skillPercentage = 0;
        $this->skillSortOrder = 0;
        $this->skillIsActive = true;
        $this->dispatch('modal-show', name: 'skill-form');
    }

    public function saveSkill(): void
    {
        $this->validate([
            'skillName' => ['required', 'string', 'max:255'],
            'skillDescription' => ['nullable', 'string', 'max:65535'],
            'skillPercentage' => ['required', 'integer', 'min:0', 'max:100'],
            'skillSortOrder' => ['required', 'integer', 'min:0'],
            'skillIsActive' => ['boolean'],
        ]);

        $data = [
            'name' => $this->skillName,
            'description' => $this->skillDescription,
            'percentage' => $this->skillPercentage,
            'sort_order' => $this->skillSortOrder,
            'is_active' => $this->skillIsActive,
        ];

        if ($this->editSkillId) {
            Skill::findOrFail($this->editSkillId)->update($data);
        } else {
            Skill::create($data);
        }

        $this->loadSkills();
        $this->resetSkillForm();
        Flux::modal('skill-form')->close();
        Flux::toast(
            heading: 'Skill saved',
            text: 'Skill has been added successfully.',
            variant: 'success'
        );
    }

    private function resetSkillForm(): void
    {
        $this->editSkillId = null;
        $this->skillName = '';
        $this->skillDescription = '';
        $this->skillPercentage = 0;
        $this->skillSortOrder = 0;
        $this->skillIsActive = true;
    }

    public function editSkill(int $id): void
    {
        $skill = Skill::findOrFail($id);
        $this->editSkillId = $skill->id;
        $this->skillName = $skill->name;
        $this->skillDescription = $skill->description ?? '';
        $this->skillPercentage = $skill->percentage;
        $this->skillSortOrder = $skill->sort_order;
        $this->skillIsActive = $skill->is_active;
        Flux::modal('skill-form')->show();
    }

    public function confirmDeleteSkill(int $id): void
    {
        $this->deleteSkillId = $id;
        Flux::modal('delete-skill-confirm')->show();
    }

    public function deleteSkill(): void
    {
        if (!$this->deleteSkillId) {
            return;
        }

        Skill::findOrFail($this->deleteSkillId)->delete();
        $this->loadSkills();
        $this->deleteSkillId = null;
        Flux::modal('delete-skill-confirm')->close();
        Flux::toast(
            heading: 'Skill deleted',
            text: 'Skill has been removed successfully.',
            variant: 'success'
        );
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('About') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the about section content and skills.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- ==================== ABOUT FORM ==================== --}}
    <form wire:submit="save" class="mt-8 max-w-2xl space-y-8">

        <div>
            <flux:heading level="3" size="lg">{{ __('Section Header') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Heading') }}</flux:label>
                <flux:input wire:model="heading" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="2" />
            </flux:field>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Content') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('Subtitle') }}</flux:label>
                <flux:input wire:model="subtitle" type="text" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Paragraph 1') }}</flux:label>
                <flux:textarea wire:model="paragraph1" rows="4" />
            </flux:field>
            <flux:field class="mt-4">
                <flux:label>{{ __('Paragraph 2') }}</flux:label>
                <flux:textarea wire:model="paragraph2" rows="4" />
            </flux:field>
        </div>

        @php $cdnUrl = config('filesystems.disks.cdn.url'); @endphp

        {{-- Profile Image --}}
        <div>
            <flux:heading level="3" size="lg">{{ __('Profile Image') }}</flux:heading>
            <flux:text class="mt-1 mb-4">{{ __('Upload a profile image for the about section.') }}</flux:text>

            <div class="flex items-center gap-6" x-data>
                <div class="shrink-0">
                    <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                        <img src="{{ $profileImagePath && !str_starts_with($profileImagePath, 'asset/') ? $cdnUrl . '/' . ltrim($profileImagePath, '/') : asset($profileImagePath && str_starts_with($profileImagePath, 'asset/') ? $profileImagePath : 'asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}" id="about-profile-preview">
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        x-ref="profileImageInput"
                        @change="
                            const file = $event.target.files[0];
                            if (!file) return;
                            const status = $el.closest('.flex').querySelector('.profile-status');
                            status.textContent = 'Processing...';
                            window.cdnUploader.fullPipeline(
                                file, 'about',
                                () => $wire.generatePresignedUrl('profileImagePath'),
                                (path) => $wire.set('profileImagePath', path)
                            ).then((path) => {
                                status.textContent = 'Upload complete';
                                document.getElementById('about-profile-preview').src = '{{ $cdnUrl }}/' + (path.charAt(0) === '/' ? path.slice(1) : path);
                            }).catch((err) => {
                                status.textContent = 'Upload failed: ' + err.message;
                            });
                        "
                    />

                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="primary" x-on:click="$refs.profileImageInput.click()">
                            {{ __('Upload Image') }}
                        </flux:button>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span class="profile-status">
                            @if ($profileImagePath && !str_starts_with($profileImagePath, 'asset/'))
                                {{ __('CDN image loaded') }}
                            @elseif ($profileImagePath)
                                {{ __('Current image loaded') }}
                            @else
                                {{ __('No file selected') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Signature Image --}}
        <div>
            <flux:heading level="3" size="lg">{{ __('Signature Image') }}</flux:heading>
            <flux:text class="mt-1 mb-4">{{ __('Upload a signature image for the about section.') }}</flux:text>

            <div class="flex items-center gap-6" x-data>
                <div class="shrink-0">
                    <div style="width:150px;height:150px;overflow:hidden;border-radius:7px;flex:0 0 150px;">
                        <img src="{{ $signatureImagePath && !str_starts_with($signatureImagePath, 'asset/') ? $cdnUrl . '/' . ltrim($signatureImagePath, '/') : asset($signatureImagePath && str_starts_with($signatureImagePath, 'asset/') ? $signatureImagePath : 'asset/img/preview-images-kosong.png') }}" style="width:100%;height:100%;object-fit:cover;display:block;" alt="{{ __('Preview') }}" id="about-signature-preview">
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        x-ref="signatureImageInput"
                        @change="
                            const file = $event.target.files[0];
                            if (!file) return;
                            const status = $el.closest('.flex').querySelector('.signature-status');
                            status.textContent = 'Processing...';
                            window.cdnUploader.fullPipeline(
                                file, 'about',
                                () => $wire.generatePresignedUrl('signatureImagePath'),
                                (path) => $wire.set('signatureImagePath', path)
                            ).then((path) => {
                                status.textContent = 'Upload complete';
                                document.getElementById('about-signature-preview').src = '{{ $cdnUrl }}/' + (path.charAt(0) === '/' ? path.slice(1) : path);
                            }).catch((err) => {
                                status.textContent = 'Upload failed: ' + err.message;
                            });
                        "
                    />

                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="primary" x-on:click="$refs.signatureImageInput.click()">
                            {{ __('Upload Image') }}
                        </flux:button>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <span class="signature-status">
                            @if ($signatureImagePath && !str_starts_with($signatureImagePath, 'asset/'))
                                {{ __('CDN image loaded') }}
                            @elseif ($signatureImagePath)
                                {{ __('Current image loaded') }}
                            @else
                                {{ __('No file selected') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <flux:heading level="3" size="lg">{{ __('Signature') }}</flux:heading>
            <div class="grid gap-4 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="signatureName" type="text" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Title') }}</flux:label>
                    <flux:input wire:model="signatureTitle" type="text" />
                </flux:field>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading level="3" size="lg">{{ __('Info Items') }}</flux:heading>
                    <flux:text class="mt-1 mb-4">{{ __('Personal information displayed in the about section.') }}</flux:text>
                </div>
                <flux:button variant="primary" size="sm" wire:click="addInfoItem" type="button">
                    {{ __('Add Item') }}
                </flux:button>
            </div>

            <div class="space-y-3">
                @foreach($infoItems as $index => $item)
                    <div class="flex items-end gap-3 rounded-lg border p-4">
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Label') }}</flux:label>
                            <flux:input wire:model="infoItems.{{ $index }}.label" type="text" />
                        </flux:field>
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Value') }}</flux:label>
                            <flux:input wire:model="infoItems.{{ $index }}.value" type="text" />
                        </flux:field>
                        <flux:button variant="ghost" size="sm" wire:click="removeInfoItem({{ $index }})" type="button" icon="trash" />
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

    {{-- ==================== SKILLS SECTION ==================== --}}
    <div class="mt-16">
        <flux:separator variant="subtle" class="mb-8" />

        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading level="3" size="lg">{{ __('Skills') }}</flux:heading>
                <flux:text class="mt-1">{{ __('List of skills shown on your website.') }}</flux:text>
            </div>
            <flux:button variant="primary" wire:click="addSkill">{{ __('Add Skill') }}</flux:button>
        </div>

        @if(count($skills) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">{{ __('No skills found.') }}</div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">{{ __('Skill') }}</th>
                            <th class="px-4 py-3">{{ __('Percentage') }}</th>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skills as $skill)
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $skill['name'] }}</td>
                                <td class="px-4 py-3">{{ $skill['percentage'] }}%</td>
                                <td class="px-4 py-3">{{ $skill['sort_order'] }}</td>
                                <td class="px-4 py-3">
                                    @if($skill['is_active'])
                                        <span class="text-green-600">Active</span>
                                    @else
                                        <span class="text-red-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button variant="ghost" size="sm" wire:click="editSkill({{ $skill['id'] }})" wire:loading.attr="disabled" icon="pencil-square" tooltip="Edit Skill" />
                                    <flux:button variant="danger" size="sm" wire:click="confirmDeleteSkill({{ $skill['id'] }})" wire:loading.attr="disabled" icon="trash" tooltip="Delete Skill" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Skill Form Modal --}}
    <flux:modal name="skill-form" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editSkillId ? __('Edit Skill') : __('Add Skill') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the skill information.') }}</flux:text>
            </div>
            <flux:field>
                <flux:label>{{ __('Skill Name') }}</flux:label>
                <flux:input wire:model="skillName" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="skillDescription" rows="2" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Percentage') }}</flux:label>
                <flux:input wire:model="skillPercentage" type="number" min="0" max="100" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Display Order') }}</flux:label>
                <flux:input wire:model="skillSortOrder" type="number" min="0" />
            </flux:field>
            <flux:field>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="skillIsActive">
                    <span>{{ __('Active') }}</span>
                </label>
            </flux:field>
            <div class="flex gap-2">
                <flux:button variant="primary" wire:click="saveSkill" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'skill-form' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Skill Confirm Modal --}}
    <flux:modal name="delete-skill-confirm" class="min-w-[24rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Skill') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Are you sure you want to delete this skill? This action cannot be undone.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="deleteSkill" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Delete') }}</span>
                    <span wire:loading>{{ __('Deleting...') }}</span>
                </flux:button>
                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'delete-skill-confirm' })">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
