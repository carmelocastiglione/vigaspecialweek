<?php

use App\Models\SchoolClass;
use App\Enums\Track;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Modifica Classe')]
    public SchoolClass $schoolClass;
    public ?string $internal_id = '';
    public int $year = 1;
    public string $section = '';
    public ?string $track = '';

    public function mount(SchoolClass $schoolClass): void
    {
        $this->schoolClass = $schoolClass;
        $this->internal_id = $schoolClass->internal_id;
        $this->year = $schoolClass->year;
        $this->section = $schoolClass->section;
        $this->track = $schoolClass->track?->value;
    }

    protected function rules(): array
    {
        return [
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:school_classes,internal_id,' . $this->schoolClass->id],
            'year' => ['required', 'integer', 'min:1', 'max:5', Rule::unique('school_classes')->where('year', $this->year)->where('section', $this->section)->ignore($this->schoolClass->id)],
            'section' => ['required', 'string', 'max:2'],
            'track' => ['nullable', 'string', 'in:' . Track::values()],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->schoolClass);
        
        $validated = $this->validate();

        $this->schoolClass->update([
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
            'year' => $validated['year'],
            'section' => $validated['section'],
            'track' => filled($validated['track']) ? $validated['track'] : null,
        ]);

        session()->flash('status', 'Classe aggiornata con successo!');
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
        <flux:heading size="lg">Modifica Classe</flux:heading>
        <p class="text-gray-600 mt-2">{{ $schoolClass->year }}{{ $schoolClass->section }}</p>
    </div>

    <!-- Form -->
    <flux:card>
        <form wire:submit="save" class="space-y-6">

            <!-- ID Interno -->
            <flux:field>
                <flux:label>ID Interno</flux:label>
                <flux:input 
                    wire:model="internal_id"
                    type="text"
                    placeholder="es. 1A"
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
                    maxlength="2"
                    placeholder="es. A"
                />
                <flux:error name="section" />
            </flux:field>

            <!-- Indirizzo -->
            <flux:field>
                <flux:label>Indirizzo <span class="text-red-500 ml-1">*</span></flux:label>
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
                    Salva Modifiche
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>