<?php

use App\Models\SchoolClass;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Title('Gestione Classi')]
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;

    public ?SchoolClass $schoolClassToDelete = null;

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

    public function confirmDelete(SchoolClass $schoolClass): void
    {
        $this->authorize('delete', $schoolClass);
        $this->schoolClassToDelete = $schoolClass;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->schoolClassToDelete) {
            return;
        }

        $this->authorize('delete', $this->schoolClassToDelete);

        $this->schoolClassToDelete->delete();
        $this->schoolClassToDelete = null;
        $this->showDeleteModal = false;

        session()->flash('status', [
            'message' => 'Classe eliminata con successo!', 
            'variant' => 'success'
        ]);
    }

    public function with(): array
    {
        $this->authorize('viewAny', SchoolClass::class);

        $schoolClasses = SchoolClass::query()
            ->when($this->search, function ($query) {
                if (is_numeric($this->search)) {
                    $query->where('year', $this->search)
                          ->orWhere('section', 'ilike', "%{$this->search}%");
                } else {
                    $query->where('section', 'ilike', "%{$this->search}%");
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return [
            'schoolClasses' => $schoolClasses,
        ];
    }
};

?>

<div>
    <x-status-toast />

    <!-- Header --> 
    <x-page-header 
        title="Classi"
        description="Gestisci le classi"
        createRoute="school-classes.create"
        createLabel="Nuova Classe"
    />

    <!-- Barra di Ricerca -->
    <flux:card class="mb-6">
        <flux:field>
            <flux:label>Ricerca</flux:label>
            <flux:input 
                wire:model.live="search"
                type="text"
                placeholder="Cerca per anno o sezione..."
                icon="magnifying-glass"
            />
        </flux:field>
    </flux:card>

    <!-- Tabella Classi -->
    <flux:card>
        @if ($schoolClasses->count() > 0)
            <flux:table :paginate="$schoolClasses">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortField === 'year'" :direction="$sortDirection" wire:click="sortBy('year')">Anno</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'section'" :direction="$sortDirection" wire:click="sortBy('section')">Sezione</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'internal_id'" :direction="$sortDirection" wire:click="sortBy('internal_id')">ID Interno</flux:table.column>
                    <flux:table.column >Indirizzo</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">Data Creazione</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                @foreach ($schoolClasses as $schoolClass)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold">
                            {{ $schoolClass->year }}
                        </flux:table.cell>
                        <flux:table.cell class="font-semibold">
                            {{ $schoolClass->section }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $schoolClass->internal_id }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $schoolClass->track?->label() }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $schoolClass->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>
                                <flux:navmenu>
                                    <flux:navmenu.item href="{{ route('school-classes.edit', $schoolClass) }}" icon="pencil-square">Modifica</flux:navmenu.item>
                                    <flux:navmenu.item wire:click="confirmDelete({{ $schoolClass->id }})" href="#" icon="trash" variant="danger">Elimina</flux:navmenu.item>
                                </flux:navmenu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Nessuna aula trovata</p>
            </div>
        @endif
    </flux:card>

    <!-- Modal Conferma Eliminazione -->
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">Elimina Aula</flux:heading>
        
        @if ($schoolClassToDelete)
            <p class="text-gray-600 mt-4">
                Sei sicuro di voler eliminare <strong>{{ $schoolClassToDelete->year }}{{ $schoolClassToDelete->section }}</strong>?
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