<?php

use App\Models\Activity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

it('shows only activities proposed by the teacher with the correct number of likes', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $other = User::factory()->create();
    $first = Activity::factory()->create(['title' => 'Algebra', 'user_id' => $teacher->id]);
    $second = Activity::factory()->create(['title' => 'Biologia', 'user_id' => $teacher->id]);
    Activity::factory()->create(['title' => 'Chimica', 'user_id' => $other->id, 'secondary_user_id' => $teacher->id]);
    Activity::factory()->create(['title' => 'Danza', 'user_id' => null]);
    $first->likedByUsers()->attach(User::factory()->count(2)->create());

    $this->actingAs($teacher)->get(route('teacher.activities'))
        ->assertSeeInOrder(['Algebra', 'aria-label="2 mi piace"', 'Biologia', 'aria-label="0 mi piace"'], false)
        ->assertDontSee('Chimica')
        ->assertDontSee('Danza');

    Livewire::actingAs($teacher)->test('pages::teacher.activities')
        ->assertViewHas('activities', fn ($activities) => $activities->getCollection()->firstWhere('id', $first->id)->liked_by_users_count === 2
            && $activities->getCollection()->firstWhere('id', $second->id)->liked_by_users_count === 0);

    $this->get(route('dashboard'))->assertSee(route('teacher.activities'));
});

it('shows an empty state when the teacher has proposed no activities', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    Activity::factory()->create();

    $this->actingAs($teacher)->get(route('teacher.activities'))
        ->assertSee('Nessuna attività proposta.');
});

it('escapes activity titles', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    Activity::factory()->create(['title' => '<script>alert(1)</script>', 'user_id' => $teacher->id]);

    $this->actingAs($teacher)->get(route('teacher.activities'))
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('allows teachers to browse all their activities across pages', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    Activity::factory()->count(16)
        ->sequence(fn (Sequence $sequence) => ['title' => sprintf('Proposta %02d', $sequence->index)])
        ->create(['user_id' => $teacher->id]);

    Livewire::actingAs($teacher)->test('pages::teacher.activities')
        ->assertSee('Proposta 00')
        ->assertDontSee('Proposta 15')
        ->call('nextPage')
        ->assertSee('Proposta 15')
        ->assertDontSee('Proposta 00');
});

it('redirects guests and refuses users without the teacher role', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->get(route('teacher.activities'))->assertRedirect(route('login'));
    $this->actingAs($user)->get(route('teacher.activities'))->assertForbidden();
})->with(['student', 'admin']);

it('rechecks the teacher role on subsequent requests', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $component = Livewire::actingAs($teacher)->test('pages::teacher.activities');

    $teacher->removeRole('teacher');

    $component->call('$refresh')->assertForbidden();
});
