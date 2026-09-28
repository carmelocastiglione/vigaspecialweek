<?php

use App\Models\Classroom;
use App\Enums\ClassroomType;
use App\Enums\ClassroomBuilding;
use App\Enums\ClassroomFloor;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Modifica Aula')]
    public Classroom $classroom;
    public string $description = '';
    public ?string $internal_id = '';
    public int $capacity = 20;
    public string $type = '';
    public string $building = '';
    public string $floor = '';

    public function mount(Classroom $classroom): void
    {
        $this->classroom = $classroom;
        $this->description = $classroom->description;
        $this->internal_id = $classroom->internal_id;
        $this->capacity = $classroom->capacity;
        $this->type = $classroom->type->value;
        $this->building = $classroom->building->value;
        $this->floor = $classroom->floor->value;
    }

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:classrooms,internal_id,' . $this->classroom->id],
            'capacity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:' . ClassroomType::values()],
            'building' => ['required', 'string', 'in:' . ClassroomBuilding::values()],
            'floor' => ['required', 'string', 'in:' . ClassroomFloor::values()],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->classroom);
        
        $validated = $this->validate();

        $this->classroom->update([
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'capacity' => $validated['capacity'],
            'type' => $validated['type'],
            'building' => $validated['building'],
            'floor' => $validated['floor'],
        ]);

        session()->flash('status', 'Aula aggiornata con successo!');
        $this->redirect(route('classrooms.index'));
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Modifica Aula</flux:heading>
        <p class="text-gray-600 mt-2">{{ $classroom->description }}</p>
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
                    placeholder="es. T.01"
                />
                <flux:error name="description" />
            </flux:field>

            <!-- ID Interno -->
            <flux:field>
                <flux:label>ID Interno</flux:label>
                <flux:input 
                    wire:model="internal_id"
                    type="text"
                    placeholder="es. T.01"
                />
                <flux:error name="internal_id" />
            </flux:field>

            <!-- Capacità -->
            <flux:field>
                <flux:label>Capacità <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="capacity"
                    type="number"
                    min="1"
                    step="1"
                    value="20"
                />
                <flux:error name="capacity" />
            </flux:field>

            <!-- Tipo -->
            <flux:field>
                <flux:label>Tipo <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:select wire:model="type">
                    @foreach (ClassroomType::cases() as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <!-- Edificio -->
            <flux:field>
                <flux:label>Edificio <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:select wire:model="building">
                    @foreach (ClassroomBuilding::cases() as $building)
                        <option value="{{ $building->value }}">{{ $building->label() }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="building" />
            </flux:field>

            <!-- Piano -->
            <flux:field>
                <flux:label>Piano <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:select wire:model="floor">
                    @foreach (ClassroomFloor::cases() as $floor)
                        <option value="{{ $floor->value }}">{{ $floor->label() }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="floor" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('classrooms.index') }}"
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