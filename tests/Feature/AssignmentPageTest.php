<?php

use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Services\UserProfileManager;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function assignmentAdmin(): User
{
    test()->seed(RoleAndPermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

it('assigns the assignment permissions only to the admin role when seeded', function () {
    $admin = assignmentAdmin();
    Role::findByName('admin')->revokePermissionTo('assignments.view');

    $this->seed(RoleAndPermissionSeeder::class);

    expect($admin->fresh()->can('assignments.view'))->toBeTrue()
        ->and($admin->fresh()->can('assignments.create'))->toBeTrue()
        ->and($admin->fresh()->can('assignments.delete'))->toBeTrue()
        ->and(Role::findByName('teacher')->hasPermissionTo('assignments.view'))->toBeFalse()
        ->and(Role::findByName('student')->hasPermissionTo('assignments.view'))->toBeFalse();
});

it('restricts the assignments page and actions to administrators', function () {
    $admin = assignmentAdmin();
    $student = User::factory()->create();
    $student->assignRole('student');

    $this->get(route('assignments.index'))->assertRedirect(route('login'));
    $this->actingAs($student)->get(route('assignments.index'))->assertForbidden();

    $component = Livewire::actingAs($admin)->test('pages::assignments.index');
    $this->actingAs($student);
    $component->call('removeAssignment', 1)->assertForbidden();
});

it('denies access and hides navigation when an admin lacks the view permission', function () {
    $admin = assignmentAdmin();
    $component = Livewire::actingAs($admin)->test('pages::assignments.index');
    Role::findByName('admin')->revokePermissionTo('assignments.view');

    $component->call('removeAssignment', 1)->assertForbidden();
    $this->actingAs($admin)->get(route('assignments.index'))->assertForbidden();
    $this->get(route('dashboard'))->assertDontSee(route('assignments.index'));
});

it('enforces create and delete permissions independently of the view permission', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $assignment = $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);
    Role::findByName('admin')->revokePermissionTo(['assignments.create', 'assignments.delete']);

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->assertDontSee('Aggiungi assegnazione')
        ->assertDontSee('Rimuovi')
        ->call('openAssignmentModal', $teacher->id)
        ->assertForbidden();

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('addAssignment')
        ->assertForbidden();

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('removeAssignment', $assignment->id)
        ->assertForbidden();

    $this->assertModelExists($assignment);
});

it('does not grant a non-admin access even with assignment permissions', function () {
    assignmentAdmin();
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $teacher->givePermissionTo('assignments.view', 'assignments.create', 'assignments.delete');

    $this->actingAs($teacher)->get(route('assignments.index'))->assertForbidden();
    Livewire::actingAs($teacher)->test('pages::assignments.index')->assertForbidden();
});

it('permits deletion when only the create permission is revoked', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $assignment = $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);
    Role::findByName('admin')->revokePermissionTo('assignments.create');

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->assertDontSee('Aggiungi assegnazione')
        ->assertSee('Rimuovi')
        ->call('removeAssignment', $assignment->id);

    $this->assertModelMissing($assignment);
});

it('permits creation when only the delete permission is revoked', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    Role::findByName('admin')->revokePermissionTo('assignments.delete');

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->assertSee('Aggiungi assegnazione')
        ->call('openAssignmentModal', $teacher->id)
        ->set('schoolClassId', (string) $schoolClass->id)
        ->set('subjectId', (string) $subject->id)
        ->call('addAssignment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('teaching_assignments', [
        'teacher_profile_id' => $teacher->id,
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
    ]);
});

it('lists and searches teachers with their assigned class and subject', function () {
    $admin = assignmentAdmin();
    $user = User::factory()->create(['name' => 'Mario', 'surname' => 'Rossi']);
    $teacher = TeacherProfile::factory()->create(['user_id' => $user->id]);
    $other = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create(['description' => '3G']);
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id, 'description' => 'Informatica']);
    $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->assertSee($user->email)
        ->assertSee($other->user->email)
        ->assertSee('3G')
        ->assertSee('Informatica')
        ->assertSee('Nessuna assegnazione')
        ->set('search', 'ROSSI')
        ->assertSee($user->email)
        ->assertDontSee($other->user->email);
});

it('shows teachers without a profile and explains how to enable assignments', function () {
    $admin = assignmentAdmin();
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->assertSee($teacher->email)
        ->assertSee('Per aggiungere assegnazioni, crea prima il')
        ->assertSee(route('users.edit', $teacher));
});

it('links the assignments page from the admin dashboard', function () {
    $admin = assignmentAdmin();

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertSee('Assegnazioni')
        ->assertSee(route('assignments.index'));

    $this->get(route('assignments.index'))->assertOk();
});

it('assigns the same class and subject to multiple teachers across departments and rejects duplicates', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $otherTeacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $otherDepartment = Department::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $otherDepartment->id]);

    $component = Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('openAssignmentModal', $teacher->id)
        ->set('schoolClassId', (string) $schoolClass->id)
        ->set('subjectId', (string) $subject->id)
        ->call('addAssignment')
        ->assertHasNoErrors()
        ->assertSet('showAssignmentModal', false)
        ->assertSee($subject->description);

    $component->call('openAssignmentModal', $teacher->id)
        ->set('schoolClassId', (string) $schoolClass->id)
        ->set('subjectId', (string) $subject->id)
        ->call('addAssignment')
        ->assertHasErrors(['subjectId'])
        ->assertSee('Questa classe e materia sono già assegnate al docente.');

    $component->call('openAssignmentModal', $otherTeacher->id)
        ->set('schoolClassId', (string) $schoolClass->id)
        ->set('subjectId', (string) $subject->id)
        ->call('addAssignment')
        ->assertHasNoErrors();

    $this->assertDatabaseCount('teaching_assignments', 2);
    $this->assertDatabaseHas('teaching_assignments', ['teacher_profile_id' => $teacher->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);
    $this->assertDatabaseHas('teaching_assignments', ['teacher_profile_id' => $otherTeacher->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);
});

it('rejects invalid class and subject selections', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('openAssignmentModal', $teacher->id)
        ->call('addAssignment')
        ->assertHasErrors(['schoolClassId' => 'required', 'subjectId' => 'required'])
        ->set('schoolClassId', '999999')
        ->set('subjectId', '999999')
        ->call('addAssignment')
        ->assertHasErrors(['schoolClassId' => 'exists', 'subjectId' => 'exists']);

    $this->assertDatabaseCount('teaching_assignments', 0);
});

it('removes only the selected assignment', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $assignment = $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('removeAssignment', $assignment->id)
        ->assertSee('Nessuna assegnazione');

    $this->assertModelMissing($assignment);
});

it('prevents a profile change from silently deleting teaching assignments', function () {
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $assignment = $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);

    try {
        app(UserProfileManager::class)->save($teacher->user, 'student', ['school_class_id' => $schoolClass->id]);
        $this->fail('The profile change should have been rejected.');
    } catch (ValidationException $exception) {
        expect($exception->errors()['profile.type'])->toContain('Rimuovi le assegnazioni del docente prima di cambiare il tipo di profilo.');
    }

    $this->assertModelExists($assignment);
    $this->assertModelExists($teacher);
});

it('enforces uniqueness of each teacher class and subject combination in the database', function () {
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $attributes = ['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id];
    $teacher->teachingAssignments()->create($attributes);

    expect(fn () => $teacher->teachingAssignments()->create($attributes))
        ->toThrow(UniqueConstraintViolationException::class);
});

it('prevents deleting a class or subject while it has assignments', function () {
    $admin = assignmentAdmin();
    $teacher = TeacherProfile::factory()->create();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);

    Livewire::actingAs($admin)->test('pages::school-classes.index')
        ->call('confirmDelete', $schoolClass->id)
        ->call('delete')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Rimuovi le assegnazioni di questa classe prima di eliminarla.');

    Livewire::actingAs($admin)->test('pages::subjects.index')
        ->call('confirmDelete', $subject->id)
        ->call('delete')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Rimuovi le assegnazioni di questa materia prima di eliminarla.');

    $this->assertModelExists($schoolClass);
    $this->assertModelExists($subject);
});
