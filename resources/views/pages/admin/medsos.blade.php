<?php

use App\Models\Site;
use App\Models\SocialMedia;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Medsos settings')] class extends Component {
    public array $socialMedia = [];

    // Add / Edit form state
    public ?int $editSocialMediaId = null;
    public string $editIcon = '';
    public string $editName = '';
    public string $editUrl = '';

    // Delete state
    public ?int $deleteSocialMediaId = null;

    /**
     * All available platforms with their display name and Bootstrap icon class.
     *
     * @return array<string, array{name: string, icon: string}>
     */
    private function platformOptions(): array
    {
        return [
            'twitter'   => ['name' => 'Twitter/X',   'icon' => 'bi-twitter-x'],
            'facebook'  => ['name' => 'Facebook',     'icon' => 'bi-facebook'],
            'instagram' => ['name' => 'Instagram',    'icon' => 'bi-instagram'],
            'linkedin'  => ['name' => 'LinkedIn',     'icon' => 'bi-linkedin'],
            'tiktok'    => ['name' => 'TikTok',       'icon' => 'bi-tiktok'],
            'blog'      => ['name' => 'Blog',         'icon' => 'bi-pencil-square'],
        ];
    }

    /**
     * Resolve the Bootstrap icon class for a given platform identifier.
     */
    private function iconClass(string $identifier): string
    {
        return $this->platformOptions()[$identifier]['icon'] ?? 'bi-link-45deg';
    }

    public function mount(): void
    {
        $this->loadSocialMedia();
    }

    /**
     * Reload social media records from the database.
     */
    private function loadSocialMedia(): void
    {
        $this->socialMedia = Site::firstOrFail()->socialMedia->toArray();
    }

    /**
     * Auto-fill the social media name when the platform selection changes.
     */
    public function updatedEditIcon(string $value): void
    {
        $platforms = $this->platformOptions();

        $this->editName = $platforms[$value]['name'] ?? '';
    }

    /**
     * Platforms that are already in use (disabled in the Add form).
     * When editing, the current record's own platform is excluded.
     *
     * @return list<string>
     */
    public function usedPlatformIcons(): array
    {
        $used = collect($this->socialMedia)->pluck('medsos_icon')->toArray();

        if ($this->editSocialMediaId !== null) {
            $used = array_values(array_filter(
                $used,
                fn (string $icon) => $icon !== $this->editIcon,
            ));
        }

        return $used;
    }

    /**
     * Open the modal to add a new social media record.
     */
    public function addSocialMedia(): void
    {
        $this->editSocialMediaId = null;
        $this->editIcon = '';
        $this->editName = '';
        $this->editUrl = '';

        $this->dispatch('modal-show', name: 'social-media-form');
    }

    /**
     * Open the modal to edit an existing social media record.
     */
    public function editSocialMedia(int $id): void
    {
        $sm = Site::firstOrFail()->socialMedia()->findOrFail($id);

        $this->editSocialMediaId = $id;
        $this->editIcon = $sm->medsos_icon;
        $this->editName = $sm->medsos_name;
        $this->editUrl = $sm->medsos_url;

        $this->dispatch('modal-show', name: 'social-media-form');
    }

    /**
     * Save (create or update) a social media record.
     */
    public function saveSocialMedia(): void
    {
        $site = Site::firstOrFail();

        $this->validate([
            'editIcon' => [
                'required',
                Rule::in(array_keys($this->platformOptions())),
                Rule::unique('social_media', 'medsos_icon')
                    ->where('site_id', $site->id)
                    ->ignore($this->editSocialMediaId),
            ],
            'editUrl' => ['required', 'url', 'max:255'],
        ]);

        if ($this->editSocialMediaId) {
            $site->socialMedia()->where('id', $this->editSocialMediaId)->update([
                'medsos_icon' => $this->editIcon,
                'medsos_name' => $this->editName,
                'medsos_url' => $this->editUrl,
            ]);
        } else {
            $site->socialMedia()->create([
                'medsos_icon' => $this->editIcon,
                'medsos_name' => $this->editName,
                'medsos_url' => $this->editUrl,
            ]);
        }

        $this->dispatch('modal-close', name: 'social-media-form');
        $this->loadSocialMedia();

        Flux::toast(variant: 'success', text: __('Social media saved.'));
    }

    /**
     * Open the delete confirmation modal.
     */
    public function confirmDeleteSocialMedia(int $id): void
    {
        $this->deleteSocialMediaId = $id;

        $this->dispatch('modal-show', name: 'confirm-deletion');
    }

    /**
     * Delete a social media record.
     */
    public function deleteSocialMedia(): void
    {
        Site::firstOrFail()->socialMedia()
            ->where('id', $this->deleteSocialMediaId)
            ->delete();

        $this->deleteSocialMediaId = null;

        $this->dispatch('modal-close', name: 'confirm-deletion');
        $this->loadSocialMedia();

        Flux::toast(variant: 'success', text: __('Social media deleted.'));
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Medsos') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage the social media links displayed in the guest site header.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- Social Media --}}
    <div class="mt-8 max-w-3xl">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading level="3" size="lg">{{ __('Social Media') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Social media links shown in the header.') }}</flux:text>
            </div>

            <flux:button variant="primary" wire:click="addSocialMedia">
                {{ __('Add Social Media') }}
            </flux:button>
        </div>

        @if (count($socialMedia) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">
                {{ __('No social media links yet.') }}
            </div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Platform') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('URL') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($socialMedia as $sm)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                <td class="px-4 py-3">
                                    <i class="{{ $this->iconClass($sm['medsos_icon']) }} me-2"></i>
                                </td>
                                <td class="px-4 py-3">{{ $sm['medsos_name'] }}</td>
                                <td class="max-w-[240px] truncate px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $sm['medsos_url'] }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <flux:button variant="ghost" size="sm" wire:click="editSocialMedia({{ $sm['id'] }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button variant="danger" size="sm" wire:click="confirmDeleteSocialMedia({{ $sm['id'] }})">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Add / Edit Modal --}}
    <flux:modal name="social-media-form" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editSocialMediaId ? __('Edit Social Media') : __('Add Social Media') }}
                </flux:heading>
                <flux:text class="mt-1">{{ __('Select a platform and enter the URL.') }}</flux:text>
            </div>

            {{-- Platform / Icon --}}
            <flux:field>
                <flux:label>{{ __('Platform') }}</flux:label>

                @php $unavailable = $this->usedPlatformIcons(); @endphp

                <select
                    wire:model.live="editIcon"
                    class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm focus:border-zinc-400 focus:outline-none focus:ring-0 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200"
                >
                    <option value="">—</option>
                    @foreach ($this->platformOptions() as $id => $platform)
                        <option
                            value="{{ $id }}"
                            @disabled(in_array($id, $unavailable))
                        >
                            {{ $platform['name'] }}
                        </option>
                    @endforeach
                </select>

                <flux:error name="editIcon" />
            </flux:field>

            {{-- Name (readonly, auto-filled) --}}
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="editName" type="text" readonly class="cursor-default opacity-75" />
            </flux:field>

            {{-- URL --}}
            <flux:field>
                <flux:label>{{ __('URL') }}</flux:label>
                <flux:input wire:model.blur="editUrl" type="url" placeholder="https://" required />
                <flux:error name="editUrl" />
            </flux:field>

            <div class="flex gap-2">
                <flux:button variant="primary" wire:click="saveSocialMedia">
                    {{ __('Save') }}
                </flux:button>

                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'social-media-form' })">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="confirm-deletion" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete Social Media') }}</flux:heading>
                <flux:text class="mt-1">
                    {{ __('Are you sure you want to delete this social media record? This action cannot be undone.') }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="deleteSocialMedia">
                    {{ __('Delete') }}
                </flux:button>

                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'confirm-deletion' })">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
