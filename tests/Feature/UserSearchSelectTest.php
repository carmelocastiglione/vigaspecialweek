<?php

use App\Models\User;
use Livewire\Livewire;

it('lists users and filters them by name or surname', function () {
    User::factory()->create(['name' => 'Mario', 'surname' => 'Rossi']);
    User::factory()->create(['name' => 'Luca', 'surname' => 'Bianchi']);

    Livewire::test('user-search-select')
        ->assertSee('Rossi')
        ->assertSee('Bianchi')
        ->set('term', 'bian')
        ->assertSee('Bianchi')
        ->assertDontSee('Rossi');
});

it('stores the selected user id and shows the full name', function () {
    $user = User::factory()->create(['name' => 'Mario', 'surname' => 'Rossi']);

    Livewire::test('user-search-select')
        ->call('select', $user->id)
        ->assertSet('value', $user->id)
        ->assertSet('term', 'Mario Rossi');
});

it('clears the selection when the text is edited', function () {
    $user = User::factory()->create();

    Livewire::test('user-search-select', ['value' => $user->id])
        ->assertSet('term', "{$user->name} {$user->surname}")
        ->set('term', 'x')
        ->assertSet('value', null);
});
