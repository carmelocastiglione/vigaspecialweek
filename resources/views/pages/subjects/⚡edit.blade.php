<?php

use App\Models\Department;
use App\Models\Subject;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Modifica Materia')]
    public Subject $subject;
    public string $description = '';
    public ?string $internal_id = '';
    public ?int $department_id = null;

    public function mount(Subject $subject): void
    {
        $this->subject = $subject;
        $this->description = $subject->description;
        $this->internal_id = $subject->internal_id;
        $this->department_id = $subject->department_id;
    }

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:subjects,internal_id,' . $this->subject->id],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->subject);
        
        $validated = $this->validate();

        $this->subject->update([
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'department_id' => $validated['department_id'],
        ]);

        session()->flash('status', 'Materia aggiornata con successo!');
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
        <flux:heading size="lg">Modifica Materia</flux:heading>
        <p class="text-gray-600 mt-2">{{ $subject->description }}</p>
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
                    Salva Modifiche
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>