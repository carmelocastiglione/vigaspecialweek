<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component
{
    #[Modelable]
    public ?int $value = null;

    public string $label = 'Utente';

    public string $placeholder = 'Seleziona o cerca per nome o cognome';

    public string $term = '';

    public bool $filtering = false;

    public function mount(): void
    {
        $this->term = $this->selectedLabel();
    }

    public function updatedTerm(): void
    {
        $this->filtering = true;
        $this->value = null;
    }

    public function select(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->value = $user->id;
        $this->term = "{$user->name} {$user->surname}";
        $this->filtering = false;
    }

    public function restore(): void
    {
        $this->term = $this->selectedLabel();
        $this->filtering = false;
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function options(): Collection
    {
        $term = trim($this->term);

        return User::query()
            ->when($this->filtering && $term !== '', function ($query) use ($term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('surname', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(name, ' ', surname) like ?", ["%{$term}%"]);
                });
            })
            ->orderBy('surname')
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'surname', 'email']);
    }

    private function selectedLabel(): string
    {
        $user = $this->value ? User::find($this->value) : null;

        return $user ? "{$user->name} {$user->surname}" : '';
    }
};

?>

<flux:field
    class="relative"
    x-data="{ open: false }"
    x-on:click.outside="if (open) { open = false; $wire.restore(); }"
    x-on:keydown.escape="if (open) { open = false; $wire.restore(); }"
>
    <flux:label>{{ $label }}</flux:label>
    <flux:input
        type="text"
        autocomplete="off"
        :placeholder="$placeholder"
        wire:model.live.debounce.250ms="term"
        x-on:focus="open = true"
        x-on:click="open = true"
    />
    <ul
        x-show="open"
        x-cloak
        class="absolute z-10 top-full mt-1 w-full max-h-60 overflow-y-auto rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
    >
        @forelse ($this->options as $option)
            <li wire:key="user-option-{{ $option->id }}">
                <button
                    type="button"
                    wire:click="select({{ $option->id }})"
                    x-on:click="open = false"
                    class="w-full px-3 py-2 text-left text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700"
                >
                    {{ $option->name }} {{ $option->surname }}
                    <span class="text-zinc-500">({{ $option->email }})</span>
                </button>
            </li>
        @empty
            <li class="px-3 py-2 text-sm text-zinc-500">Nessun utente trovato</li>
        @endforelse
    </ul>
</flux:field>
