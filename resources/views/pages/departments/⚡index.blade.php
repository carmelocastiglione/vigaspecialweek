<?php

use App\Models\Department;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Title('Gestione Dipartimenti')]
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;

    public ?Department $departmentToDelete = null;

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

    public function confirmDelete(Department $department): void
    {
        $this->authorize('delete', $department);
        $this->departmentToDelete = $department;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->departmentToDelete) {
            return;
        }

        $this->authorize('delete', $this->departmentToDelete);

        $this->departmentToDelete->delete();
        $this->departmentToDelete = null;
        $this->showDeleteModal = false;

        session()->flash('status', [
            'message' => 'Dipartimento eliminato con successo!', 
            'variant' => 'success'
        ]);
    }

    public function with(): array
    {
        $this->authorize('viewAny', Department::class);

        $departments = Department::query()
            ->when($this->search, function ($query) {
                $query->where('description', 'ilike', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return [
            'departments' => $departments,
        ];
    }
};

?>

<div>
    <x-status-toast />

    <!-- Header --> 
    <x-page-header 
        title="Dipartimenti"
        description="Gestisci i dipartimenti"
        createRoute="departments.create"
        createLabel="Nuovo Dipartimento"
    />

    <!-- Barra di Ricerca -->
    <flux:card class="mb-6">
        <flux:field>
            <flux:label>Ricerca</flux:label>
            <flux:input 
                wire:model.live="search"
                type="text"
                placeholder="Cerca per descrizione..."
                icon="magnifying-glass"
            />
        </flux:field>
    </flux:card>

    <!-- Tabella Dipartimenti -->
    <flux:card>
        @if ($departments->count() > 0)
            <flux:table :paginate="$departments">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortField === 'description'" :direction="$sortDirection" wire:click="sortBy('description')">Descrizione</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'internal_id'" :direction="$sortDirection" wire:click="sortBy('internal_id')">ID Interno</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">Data Creazione</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                @foreach ($departments as $department)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold">
                            {{ $department->description }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $department->internal_id }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $department->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>
                                <flux:navmenu>
                                    <flux:navmenu.item href="{{ route('departments.edit', $department) }}" icon="pencil-square">Modifica</flux:navmenu.item>
                                    <flux:navmenu.item wire:click="confirmDelete({{ $department->id }})" href="#" icon="trash" variant="danger">Elimina</flux:navmenu.item>
                                </flux:navmenu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Nessun dipartimento trovato</p>
            </div>
        @endif
    </flux:card>

    <!-- Modal Conferma Eliminazione -->
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">Elimina Dipartimento</flux:heading>
        
        @if ($departmentToDelete)
            <p class="text-gray-600 mt-4">
                Sei sicuro di voler eliminare <strong>{{ $departmentToDelete->description }}</strong>?
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