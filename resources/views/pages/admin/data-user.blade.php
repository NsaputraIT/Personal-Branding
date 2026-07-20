<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Data User')] class extends Component {
    public array $users = [];

    // Create form state
    public string $createEmail = '';
    public string $createPassword = '';
    public string $createPasswordConfirmation = '';

    // Delete state
    public ?int $deleteUserId = null;
    public string $deleteUserEmail = '';

    public function mount(): void
    {
        $this->loadUsers();
    }

    private function loadUsers(): void
    {
        $this->users = User::select(['id', 'name', 'email', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Open the create user modal.
     */
    public function addUser(): void
    {
        $this->resetForm();
        $this->dispatch('modal-show', name: 'user-create-form');
    }

    /**
     * Save a new user.
     */
    public function saveUser(): void
    {
        $validated = $this->validate([
            'createEmail' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'createPassword' => ['required', 'string', 'min:6'],
            'createPasswordConfirmation' => ['required', 'same:createPassword'],
        ]);

        User::create([
            'name' => explode('@', $validated['createEmail'])[0],
            'email' => $validated['createEmail'],
            'password' => Hash::make($validated['createPassword']),
            'email_verified_at' => now(),
        ]);

        $this->dispatch('modal-close', name: 'user-create-form');
        $this->loadUsers();

        Flux::toast(variant: 'success', text: __('User created.'));
    }

    /**
     * Open the delete confirmation modal.
     */
    public function confirmDeleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        $this->deleteUserId = $user->id;
        $this->deleteUserEmail = $user->email;

        $this->dispatch('modal-show', name: 'confirm-deletion');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(): void
    {
        if ($this->deleteUserId === null) {
            return;
        }

        $user = User::findOrFail($this->deleteUserId);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            $this->dispatch('modal-close', name: 'confirm-deletion');
            $this->deleteUserId = null;
            $this->deleteUserEmail = '';

            Flux::toast(variant: 'danger', text: __('You cannot delete your own account.'));

            return;
        }

        $user->delete();

        $this->deleteUserId = null;
        $this->deleteUserEmail = '';

        $this->dispatch('modal-close', name: 'confirm-deletion');
        $this->loadUsers();

        Flux::toast(variant: 'success', text: __('User deleted.'));
    }

    /**
     * Reset the create form fields.
     */
    private function resetForm(): void
    {
        $this->createEmail = '';
        $this->createPassword = '';
        $this->createPasswordConfirmation = '';
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Data User') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage registered admin users.') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- Users List --}}
    <div class="mt-8 max-w-3xl">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading level="3" size="lg">{{ __('Users') }}</flux:heading>
                <flux:text class="mt-1">{{ __('All registered users who can access the admin panel.') }}</flux:text>
            </div>

            <flux:button variant="primary" wire:click="addUser">
                {{ __('Add User') }}
            </flux:button>
        </div>

        @if (count($users) === 0)
            <div class="rounded-xl border-2 border-dashed p-8 text-center text-zinc-400">
                {{ __('No users yet.') }}
            </div>
        @else
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Registered') }}</th>
                            <th class="px-4 py-3 font-medium text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                <td class="px-4 py-3">{{ $user['name'] }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $user['email'] }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Carbon::parse($user['created_at'])->format('Y-m-d') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <flux:button variant="danger" size="sm" wire:click="confirmDeleteUser({{ $user['id'] }})">
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

    {{-- Create User Modal --}}
    <flux:modal name="user-create-form" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add User') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the email and password for the new user.') }}</flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input wire:model.blur="createEmail" type="email" placeholder="email@example.com" required />
                <flux:error name="createEmail" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input wire:model.blur="createPassword" type="password" required viewable />
                <flux:error name="createPassword" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Confirm Password') }}</flux:label>
                <flux:input wire:model.blur="createPasswordConfirmation" type="password" required viewable />
                <flux:error name="createPasswordConfirmation" />
            </flux:field>

            <div class="flex gap-2">
                <flux:button variant="primary" wire:click="saveUser">
                    {{ __('Save') }}
                </flux:button>

                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'user-create-form' })">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="confirm-deletion" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                <flux:text class="mt-1">
                    {{ __('Are you sure you want to delete the user ":email"? This action cannot be undone.', ['email' => $deleteUserEmail]) }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:button variant="danger" wire:click="deleteUser">
                    {{ __('Delete') }}
                </flux:button>

                <flux:button variant="ghost" wire:click="$dispatch('modal-close', { name: 'confirm-deletion' })">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
