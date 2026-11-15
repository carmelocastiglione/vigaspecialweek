<?php

use App\Models\Activity;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Title('Gestione Attività')]
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showDeleteModal = false;

    public ?Activity $activityToDelete = null;

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

    public function confirmDelete(Activity $activity): void
    {
        $this->authorize('delete', $activity);
        $this->activityToDelete = $activity;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->activityToDelete) {
            return;
        }

        $this->authorize('delete', $this->activityToDelete);

        $this->activityToDelete->delete();
        $this->activityToDelete = null;
        $this->showDeleteModal = false;

        session()->flash('status', [
            'message' => 'Attività eliminata con successo!', 
            'variant' => 'success'
        ]);
    }

    public function with(): array
    {
        $this->authorize('viewAny', Activity::class);

        $activities = Activity::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'ilike', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return [
            'activities' => $activities,
        ];
    }
};

?>

<div>
    <x-status-toast />

    <!-- Header --> 
    <x-page-header 
        title="Attività"
        description="Gestisci le attività"
        createRoute="activities.create"
        createLabel="Nuova Attività"
    />

    <!-- Barra di Ricerca -->
    <flux:card class="mb-6">
        <flux:field>
            <flux:label>Ricerca</flux:label>
            <flux:input 
                wire:model.live="search"
                type="text"
                placeholder="Cerca per titolo..."
                icon="magnifying-glass"
            />
        </flux:field>
    </flux:card>

    <!-- Tabella Categorie -->
    <flux:card>
        @if ($activities->count() > 0)
            <flux:table :paginate="$activities">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortField === 'title'" :direction="$sortDirection" wire:click="sortBy('title')">Titolo</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'internal_id'" :direction="$sortDirection" wire:click="sortBy('internal_id')">ID Interno</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'activity_type'" :direction="$sortDirection" wire:click="sortBy('activity_type')">Tipo</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">Data Creazione</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                @foreach ($activities as $activity)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold">
                            {{ $activity->title }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $activity->internal_id }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $activity->activity_type->label() }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $activity->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="py-0">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>
                                <flux:navmenu>
                                    <flux:navmenu.item href="{{ route('activities.edit', $activity) }}" icon="pencil-square">Modifica</flux:navmenu.item>
                                    <flux:navmenu.item wire:click="confirmDelete({{ $activity->id }})" href="#" icon="trash" variant="danger">Elimina</flux:navmenu.item>
                                </flux:navmenu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Nessuna attività trovata</p>
            </div>
        @endif
    </flux:card>

    <!-- Modal Conferma Eliminazione -->
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">Elimina Attività</flux:heading>
        
        @if ($activityToDelete)
            <p class="text-gray-600 mt-4">
                Sei sicuro di voler eliminare <strong>{{ $activityToDelete->title }}</strong>?
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