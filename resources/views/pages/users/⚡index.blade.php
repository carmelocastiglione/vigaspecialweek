<?php

use App\Models\User;
use App\Services\UserProfileManager;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;

new class extends Component
{
    use WithPagination;

    private const ROLE_LABELS = [
        'admin' => 'Admin',
        'teacher' => 'Docenti',
        'student' => 'Studenti',
    ];

    #[Title('Gestione Utenti')]
    public string $search = '';

    public string $roleFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;

    public ?User $userToDelete = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function filterByRole(string $role): void
    {
        if (! array_key_exists($role, self::ROLE_LABELS)) {
            throw ValidationException::withMessages([
                'roleFilter' => 'Il ruolo selezionato non è valido.',
            ]);
        }

        $this->resetValidation('roleFilter');
        $this->roleFilter = $this->roleFilter === $role ? '' : $role;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete(User $user): void
    {
        $this->authorize('delete', $user);
        $this->userToDelete = $user;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->userToDelete) {
            return;
        }

        $this->authorize('delete', $this->userToDelete);

        try {
            app(UserProfileManager::class)->deleteUser($this->userToDelete);
        } catch (ValidationException $exception) {
            Flux::toast(text: $exception->errors()['account'][0], variant: 'danger');

            return;
        }
        $this->userToDelete = null;
        $this->showDeleteModal = false;

        session()->flash('status', [
            'message' => 'Utente eliminato con successo!', 
            'variant' => 'success'
        ]);
    }

    public function with(): array
    {
        $this->authorize('viewAny', User::class);

        $roleCards = [];
        foreach (self::ROLE_LABELS as $role => $label) {
            $roleCards[] = [
                'role' => $role,
                'label' => $label,
                'count' => User::role($role)->count(),
            ];
        }

        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('email', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter, fn ($query) => $query->role($this->roleFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return [
            'users' => $users,
            'roleCards' => $roleCards,
        ];
    }
};

?>

<div>
    <x-status-toast />

    <!-- Header -->
    <x-page-header 
        title="Utenti"
        description="Gestisci gli utenti"
        createRoute="users.create"
        createLabel="Nuovo Utente"
    />

    <!-- Barra di Ricerca -->
    <flux:card class="mb-6">
        <flux:field>
            <flux:label>Ricerca</flux:label>
            <flux:input 
                wire:model.live="search"
                type="text"
                placeholder="Cerca per nome o email..."
                icon="magnifying-glass"
            />
        </flux:field>
    </flux:card>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($roleCards as $roleCard)
            <flux:card class="p-0" wire:key="role-card-{{ $roleCard['role'] }}">
                <button
                    type="button"
                    wire:click="filterByRole('{{ $roleCard['role'] }}')"
                    aria-pressed="{{ $roleFilter === $roleCard['role'] ? 'true' : 'false' }}"
                    @class([
                        'w-full rounded-lg p-4 text-left transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600',
                        'bg-violet-50 ring-2 ring-violet-500 dark:bg-violet-950/40' => $roleFilter === $roleCard['role'],
                        'hover:bg-zinc-50 dark:hover:bg-zinc-800' => $roleFilter !== $roleCard['role'],
                    ])
                >
                    <span class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ $roleCard['label'] }}</span>
                    <span class="mt-1 block text-3xl font-semibold text-zinc-900 dark:text-white">{{ $roleCard['count'] }}</span>
                </button>
            </flux:card>
        @endforeach
    </div>

    @error('roleFilter')
        <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <!-- Tabella Utenti -->
    <flux:card>
        @if ($users->count() > 0)
            <flux:table :paginate="$users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">Nome Completo</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortDirection" wire:click="sortBy('email')">Email</flux:table.column>
                    <flux:table.column>Ruoli</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">Data Creazione</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                @foreach ($users as $user)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold">
                            {{ $user->name }} {{ $user->surname }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>
                            @forelse ($user->roles as $role)
                                <flux:badge size="sm">{{ $role->name }}</flux:badge>
                            @empty
                                <span class="text-gray-500 text-sm">Nessun ruolo</span>
                            @endforelse
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>
                                <flux:navmenu>
                                    <flux:navmenu.item href="{{ route('users.edit', $user) }}" icon="pencil-square">Modifica</flux:navmenu.item>
                                    <flux:navmenu.item wire:click="confirmDelete({{ $user->id }})" href="#" icon="trash" variant="danger">Elimina</flux:navmenu.item>
                                </flux:navmenu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Nessun utente trovato</p>
            </div>
        @endif
    </flux:card>

    <!-- Modal Conferma Eliminazione -->
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">Elimina Utente</flux:heading>
        
        @if ($userToDelete)
            <p class="text-gray-600 mt-4">
                Sei sicuro di voler eliminare <strong>{{ $userToDelete->name }} {{ $userToDelete->surname }}</strong>?
            </p>
        @endif

        <div class="flex gap-3 justify-end mt-6">
            <flux:button 
                variant="ghost"
                wire:click="$toggle('showDeleteModal')"
            >
                Annulla
            </flux:button>
            <flux:button 
                variant="danger"
                wire:click="delete"
            >
                Elimina
            </flux:button>
        </div>
    </flux:modal>
</div>