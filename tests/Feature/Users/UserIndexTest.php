<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

it('shows role counts without profile controls', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    User::factory()->count(2)->create()->each(fn (User $user) => $user->assignRole('teacher'));
    User::factory()->count(3)->create()->each(fn (User $user) => $user->assignRole('student'));

    $this->actingAs($admin);

    Livewire::test('pages::users.index')
        ->assertSee('Admin')
        ->assertSee('1 utente')
        ->assertSee('Docenti')
        ->assertSee('2 utenti')
        ->assertSee('Studenti')
        ->assertSee('3 utenti')
        ->assertDontSee('Tutti i profili')
        ->assertDontSee('Profilo');
});

it('filters and unfilters users by the selected role', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $student = User::factory()->create();
    $student->assignRole('student');

    $this->actingAs($admin);

    Livewire::test('pages::users.index')
        ->call('filterByRole', 'student')
        ->assertSet('roleFilter', 'student')
        ->assertSee($student->email)
        ->assertDontSee($teacher->email)
        ->call('filterByRole', 'student')
        ->assertSet('roleFilter', '')
        ->assertSee($teacher->email);
});
