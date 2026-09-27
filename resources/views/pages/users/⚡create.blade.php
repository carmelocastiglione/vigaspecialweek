<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new class extends Component
{
    #[Title('Crea Nuovo Utente')]
    public string $name = '';
    public string $surname = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $roles = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('create', User::class);
        
        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user = User::create($data);

        if (! empty($validated['roles'])) {
            $roleIds = $validated['roles'];
            $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
            $user->syncRoles($roles);
        }

        session()->flash('status', 'Utente creato con successo!');
        $this->redirect(route('users.index'));
    }

    public function with(): array
    {
        return [
            'roles' => Role::all(),
        ];
    }
};

?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <flux:heading size="lg">Crea Nuovo Utente</flux:heading>
        <p class="text-gray-600 mt-2">Compila il modulo per aggiungere un nuovo utente</p>
    </div>

    <!-- Form -->
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <!-- Nome -->
            <flux:field>
                <flux:label>Nome <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="name"
                    type="text"
                    placeholder="es. Giovanni"
                />
                <flux:error name="name" />
            </flux:field>

            <!-- Cognome -->
            <flux:field>
                <flux:label>Cognome <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="surname"
                    type="text"
                    placeholder="es. Rossi"
                />
                <flux:error name="surname" />
            </flux:field>

            <!-- Email -->
            <flux:field>
                <flux:label>Email <span class="text-red-500 ml-1">*</span></flux:label>
                <flux:input 
                    wire:model="email"
                    type="email"
                    placeholder="es. giovanni@example.com"
                />
                <flux:error name="email" />
            </flux:field>

            <!-- Password -->
            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input 
                    wire:model="password"
                    type="password"
                    placeholder="Minimo 8 caratteri"
                />
                <flux:error name="password" />
            </flux:field>

            <!-- Conferma Password -->
            <flux:field>
                <flux:label>Conferma Password</flux:label>
                <flux:input 
                    wire:model="password_confirmation"
                    type="password"
                    placeholder="Ripeti la password"
                />
                <flux:error name="password_confirmation" />
            </flux:field>

            <!-- Ruoli -->
            <flux:field>
                <flux:label>Ruoli</flux:label>
                <div class="space-y-3 mt-2">
                    @foreach ($roles as $role)
                        <flux:checkbox 
                            wire:model="roles"
                            value="{{ $role->id }}"
                            label="{{ ucfirst($role->name) }}"
                        />
                    @endforeach
                </div>
                <flux:error name="roles" />
            </flux:field>

            <!-- Pulsanti -->
            <div class="flex gap-3 justify-end pt-4 border-t">
                <flux:button 
                    href="{{ route('users.index') }}"
                    variant="ghost"
                >
                    Annulla
                </flux:button>
                <flux:button 
                    type="submit"
                    variant="primary"
                >
                    Crea Utente
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>