<?php

use App\Models\Department;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Crea Nuovo Dipartimento')]
    public string $description = '';
    public ?string $internal_id = '';

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:departments,internal_id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Department::class);
        
        $validated = $this->validate();

        $data = [
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
        ];

        $department = Department::create($data);

        session()->flash('status', 'Dipartimento creato con successo!');
        $this->redirect(route('departments.index'));
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Crea Nuovo Dipartimento</flux:heading>
        <p class="text-gray-600 mt-2">Compila il modulo per aggiungere un nuovo dipartimento</p>
    </div>

    <!-- Form -->
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <!-- Descrizione -->
            <flux:field>
                <flux:label>Descrizione <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="description"
                    type="text"
                    placeholder="es. Italiano"
                />
                <flux:error name="description" />
            </flux:field>

            <!-- ID Interno -->
            <flux:field>
                <flux:label>ID Interno</flux:label>
                <flux:input 
                    wire:model="internal_id"
                    type="text"
                    placeholder="es. Dip1"
                />
                <flux:error name="internal_id" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('departments.index') }}"
                    variant="ghost"
                >
                    Annulla
                </flux:button>
                <flux:button 
                    type="submit"
                    variant="primary"
                >
                    Crea Dipartimento
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>