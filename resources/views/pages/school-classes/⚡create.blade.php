<?php

use App\Models\SchoolClass;
use App\Enums\Track;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Validation\Rule;

new class extends Component
{
    #[Title('Crea Nuova Classe')]
    public string $description = '';
    public ?string $internal_id = '';
    public int $year = 1;
    public string $section = '';
    public ?string $track = '';

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:school_classes,internal_id'],
            'year' => ['required', 'integer', 'min:1', 'max:5', Rule::unique('school_classes')->where('year', $this->year)->where('section', $this->section)],
            'section' => ['required', 'string', 'max:2'],
            'track' => ['nullable', 'string', 'in:' . Track::values()],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', SchoolClass::class);
        
        $validated = $this->validate();

        $data = [
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'year' => $validated['year'],
            'section' => $validated['section'],
            'track' => filled($validated['track']) ? $validated['track'] : null,
        ];

        $schoolClass = SchoolClass::create($data);

        session()->flash('status', 'Classe creata con successo!');
        $this->redirect(route('school-classes.index'));
    }

    protected function messages(): array
    {
        return [
            'year.unique' => 'Questa combinazione di anno e sezione esiste già.',
        ];
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
                    placeholder="es. 3I"
                />
                <flux:error name="description" />
            </flux:field>

            <!-- ID Interno -->
            <flux:field>
                <flux:label>ID Interno</flux:label>
                <flux:input 
                    wire:model="internal_id"
                    type="text"
                    placeholder="es. 3I"
                />
                <flux:error name="internal_id" />
            </flux:field>
            
            <!-- Anno -->
            <flux:field>
                <flux:label>Anno <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="year"
                    type="number"
                    min="1"
                    max="5"
                    value="1"
                />
                <flux:error name="year" />
            </flux:field>

            <!-- Sezione -->
            <flux:field>
                <flux:label>Sezione <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="section"
                    type="text"
                    placeholder="es. I"
                />
                <flux:error name="section" />
            </flux:field>

            <!-- Indirizzo -->
            <flux:field>
                <flux:label>Indirizzo</flux:label>
                <flux:select wire:model="track">
                    <option value="">Seleziona un indirizzo</option>
                    @foreach (Track::cases() as $track)
                        <option value="{{ $track->value }}">{{ $track->label() }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="track" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('school-classes.index') }}"
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