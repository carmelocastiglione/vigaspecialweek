<?php

use App\Models\Classroom;
use App\Enums\ClassroomFloor;
use App\Enums\ClassroomType;
use App\Enums\ClassroomBuilding;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Crea Nuova Aula')]
    public string $description = '';
    public ?string $internal_id = '';
    public int $capacity = 20;
    public string $type = '';
    public string $building = '';
    public string $floor = '';

    public function mount(): void
    {
        $this->type = ClassroomType::AULA->value;
        $this->building = ClassroomBuilding::SEDE_CENTRALE->value;
        $this->floor = ClassroomFloor::TERRA->value;
    }

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:classrooms,internal_id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:' . ClassroomType::values()],
            'building' => ['required', 'string', 'in:' . ClassroomBuilding::values()],
            'floor' => ['required', 'string', 'in:' . ClassroomFloor::values()],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', Classroom::class);
        
        $validated = $this->validate();

        $data = [
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'capacity' => $validated['capacity'],
            'type' => $validated['type'],
            'building' => $validated['building'],
            'floor' => $validated['floor'],
        ];

        $classroom = Classroom::create($data);

        session()->flash('status', 'Aula creata con successo!');
        $this->redirect(route('classrooms.index'));
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Crea Nuova Aula</flux:heading>
        <p class="text-gray-600 mt-2">Compila il modulo per aggiungere una nuova aula</p>
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
                    Crea Aula
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>