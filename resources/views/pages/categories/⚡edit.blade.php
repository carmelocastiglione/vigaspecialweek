<?php

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Title('Modifica Categoria')]
    public Category $category;
    public string $description = '';
    public ?string $internal_id = '';

    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->description = $category->description;
        $this->internal_id = $category->internal_id;
    }

    protected function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:10', 'unique:categories,internal_id,' . $this->category->id],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->category);
        
        $validated = $this->validate();

        $this->category->update([
            'description' => $validated['description'],
            'internal_id' => filled($validated['internal_id']) ? $validated['internal_id'] : null,
        ]);

        session()->flash('status', 'Categoria aggiornata con successo!');
        $this->redirect(route('categories.index'));
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Modifica Categoria</flux:heading>
        <p class="text-gray-600 mt-2">{{ $category->description }}</p>
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
                    href="{{ route('categories.index') }}"
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