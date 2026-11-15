<?php

use App\Models\Activity;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Modifica Attività')]
    public Activity $activity;
    public string $title = '';
    public ?string $description = '';
    public ?string $internal_id = '';
    public ?int $user_id = null;

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
        $this->title = $activity->title;
        $this->description = $activity->description;
        $this->internal_id = $activity->internal_id;
        $this->user_id = $activity->user_id;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:activities,internal_id,' . $this->activity->id],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->activity);
        
        $validated = $this->validate();

        $this->activity->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        session()->flash('status', 'Attività aggiornata con successo!');
        $this->redirect(route('activities.index'));
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Modifica Attività</flux:heading>
        <p class="text-gray-600 mt-2">{{ $activity->title }}</p>
    </div>

    <!-- Form -->
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <!-- Titolo -->
            <flux:field>
                <flux:label>Titolo <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="title"
                    type="text"
                    placeholder="es. Corso di fotografia"
                />
                <flux:error name="title" />
            </flux:field>

            <!-- Descrizione -->
            <flux:field>
                <flux:label>Descrizione</flux:label>
                <flux:textarea 
                    wire:model="description"
                    placeholder="Descrizione dell'attività"
                />
                <flux:error name="description" />
            </flux:field>

            <!-- Utente -->
            <div>
                <livewire:user-search-select wire:model="user_id" />
                <flux:error name="user_id" />
            </div>

            <!-- ID Interno -->
            <flux:field>
                <flux:label>ID Interno</flux:label>
                <flux:input 
                    wire:model="internal_id"
                    type="text"
                    placeholder="es. Att1"
                />
                <flux:error name="internal_id" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('activities.index') }}"
                    variant="ghost"
                >
                    Annulla
                </flux:button>
                <flux:button 
                    type="submit"
                    variant="primary"
                >
                    Salva Modifiche
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>