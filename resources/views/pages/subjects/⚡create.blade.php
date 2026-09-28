<?php

use App\Models\Department;
use App\Models\Subject;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Crea Nuova Materia')]
    public string $description = '';
    public ?string $internal_id = '';
    public ?int $department_id = null;

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:subjects,internal_id'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Subject::class);
        
        $validated = $this->validate();

        $data = [
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'department_id' => $validated['department_id'],
        ];

        $subject = Subject::create($data);

        session()->flash('status', 'Materia creata con successo!');
        $this->redirect(route('subjects.index'));
    }

    public function with(): array
    {
        return [
            'departments' => Department::orderBy('description')->get(),
        ];
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Crea Nuova Materia</flux:heading>
        <p class="text-gray-600 mt-2">Compila il modulo per aggiungere una nuova materia</p>
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
                    placeholder="es. Mat1"
                />
                <flux:error name="internal_id" />
            </flux:field>

            <!-- Dipartimento -->
            <flux:field>
                <flux:label>Dipartimento</flux:label>
                <flux:select 
                    wire:model="department_id"
                    placeholder="Seleziona un dipartimento..."
                >
                    <option value="">-- Nessuno --</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">
                            {{ $department->description }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:error name="department_id" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('subjects.index') }}"
                    variant="ghost"
                >
                    Annulla
                </flux:button>
                <flux:button 
                    type="submit"
                    variant="primary"
                >
                    Crea Materia
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>