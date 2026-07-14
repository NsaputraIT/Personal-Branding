<?php

use App\Models\Skill;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Skills')] class extends Component
{
    /**
     * Daftar skill.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $skills = [];

    // Form state
    public ?int $editSkillId = null;
    public string $name = '';
    public int $percentage = 0;
    public int $sortOrder = 0;
    public bool $isActive = true;

    // Delete state
    public ?int $deleteSkillId = null;

    public function mount(): void
    {
        $this->loadSkills();
    }

    /**
     * Memuat ulang data skill.
     */
    private function loadSkills(): void
    {
        $this->skills = Skill::orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->toArray();
    }
};
?>

<section class="w-full">

    <flux:heading size="xl" level="1">
        {{ __('Skills') }}
    </flux:heading>

    <flux:subheading
        size="lg"
        class="mb-6"
    >
        {{ __('Manage your skills.') }}
    </flux:subheading>

    <flux:separator variant="subtle" />

    <div class="mt-8">

        @if (count($skills) === 0)

            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">
                {{ __('No skills found.') }}
            </div>

        @else

            <div class="overflow-hidden rounded-xl border">

                <table class="w-full text-left text-sm">

                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Percentage</th>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">

                        @foreach ($skills as $skill)

                            <tr>

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

                                    @if ($skill['is_active'])
                                        <span class="text-green-600">
                                            Active
                                        </span>
                                    @else
                                        <span class="text-red-600">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</section>