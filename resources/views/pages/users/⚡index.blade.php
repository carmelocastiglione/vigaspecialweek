<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Title('Gestione Utenti')]
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;

    public ?User $userToDelete = null;

    public function updatedSearch(): void
    {
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

        $this->userToDelete->delete();
        $this->userToDelete = null;
        $this->showDeleteModal = false;

        session()->flash('status', 'Utente eliminato con successo!');
    }

    public function with(): array
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return [
            'users' => $users,
        ];
    }
};

?>

<div>
    @if (session('status'))
        @php
            Flux::toast(variant: 'success', text: session('status'));
        @endphp
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="lg">Gestione Utenti</flux:heading>
        <flux:button href="{{ route('users.create') }}" icon="plus">
            Nuovo Utente
        </flux:button>
    </div>

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