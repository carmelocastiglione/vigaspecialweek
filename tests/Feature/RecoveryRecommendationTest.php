<?php

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\UserProfileManager;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

function recoveryTeacher(): TeacherProfile
{
    $teacher = TeacherProfile::factory()->create();
    $teacher->user->assignRole('teacher');

    return $teacher;
}

function recoveryAssignment(TeacherProfile $teacher, SchoolClass $schoolClass, Subject $subject): TeachingAssignment
{
    return $teacher->teachingAssignments()->create([
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
    ]);
}

it('allows teachers to independently add and remove their own recommendations', function () {
    $teacher = recoveryTeacher();
    $otherTeacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, $subject);
    $otherAssignment = recoveryAssignment($otherTeacher, $schoolClass, $subject);
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $attributes = ['teaching_assignment_id' => $assignment->id, 'student_profile_id' => $student->id];

    $component = Livewire::actingAs($teacher->user)
        ->test('pages::teacher.class-details', ['assignment' => $assignment->id])
        ->assertSeeHtml('aria-pressed="false"')
        ->call('recommendStudent', $student->id)
        ->assertSeeHtml('aria-pressed="true"')
        ->call('recommendStudent', $student->id);

    $this->assertDatabaseCount('recovery_recommendations', 1);
    $this->assertDatabaseHas('recovery_recommendations', $attributes);

    Livewire::actingAs($otherTeacher->user)
        ->test('pages::teacher.class-details', ['assignment' => $otherAssignment->id])
        ->assertSeeHtml('aria-pressed="false"')
        ->call('recommendStudent', $student->id);
    $this->assertDatabaseCount('recovery_recommendations', 2);

    Livewire::actingAs($teacher->user);
    $component->call('removeRecommendation', $student->id)
        ->assertSeeHtml('aria-pressed="false"')
        ->call('removeRecommendation', $student->id);

    $this->assertDatabaseMissing('recovery_recommendations', $attributes);
    $this->assertDatabaseHas('recovery_recommendations', [
        'teaching_assignment_id' => $otherAssignment->id,
        'student_profile_id' => $student->id,
    ]);
});

it('rejects recommendations for students outside the assigned class and for another teacher’s assignment', function () {
    $teacher = recoveryTeacher();
    $otherTeacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $outsideStudent = StudentProfile::factory()->create();
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $component = Livewire::actingAs($teacher->user)
        ->test('pages::teacher.class-details', ['assignment' => $assignment->id]);

    expect(fn () => $component->call('recommendStudent', $outsideStudent->id))->toThrow(ModelNotFoundException::class);

    Livewire::actingAs($otherTeacher->user);
    expect(fn () => $component->call('recommendStudent', $student->id))->toThrow(ModelNotFoundException::class);
    $this->assertDatabaseCount('recovery_recommendations', 0);
});

it('prevents assignment deletion while recommendations exist', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test('pages::assignments.index')
        ->call('removeAssignment', $assignment->id)
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Rimuovi le segnalazioni di recupero prima di eliminare questa assegnazione.');

    $this->assertModelExists($assignment);
    $assignment->recommendedStudents()->detach($student);
    Livewire::actingAs($admin)->test('pages::assignments.index')->call('removeAssignment', $assignment->id);
    $this->assertModelMissing($assignment);
});

it('blocks changing class or profile type until student recommendations are removed', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $newClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);
    $manager = app(UserProfileManager::class);

    try {
        $manager->save($student->user, 'student', ['school_class_id' => (string) $newClass->id]);
        $this->fail('The class change should have been rejected.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('profile.school_class_id');
    }

    try {
        $manager->save($student->user, 'teacher', ['department_id' => $teacher->department_id]);
        $this->fail('The profile change should have been rejected.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('profile.type');
    }

    $manager->save($student->user, 'student', ['school_class_id' => (string) $schoolClass->id]);
    expect($student->fresh()->school_class_id)->toBe($schoolClass->id);

    $assignment->recommendedStudents()->detach($student);
    $manager->save($student->user, 'student', ['school_class_id' => (string) $newClass->id]);
    expect($student->fresh()->school_class_id)->toBe($newClass->id);
});

it('shows a validation error in the user editor when a recommended student changes class', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $newClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test('pages::users.edit', ['user' => $student->user])
        ->set('profile.school_class_id', (string) $newClass->id)
        ->call('save')
        ->assertHasErrors(['profile.school_class_id'])
        ->assertSee('Rimuovi le segnalazioni di recupero prima di cambiare la classe.');

    expect($student->fresh()->school_class_id)->toBe($schoolClass->id);
});

it('prevents removing the teacher role while their recommendations exist', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test('pages::users.edit', ['user' => $teacher->user])
        ->set('roles', [])
        ->call('save')
        ->assertHasErrors(['roles'])
        ->assertSee('Rimuovi le segnalazioni di recupero prima di togliere il ruolo docente.');

    expect($teacher->user->fresh()->hasRole('teacher'))->toBeTrue();

    $assignment->recommendedStudents()->detach($student);
    Livewire::actingAs($admin)->test('pages::users.edit', ['user' => $teacher->user])
        ->set('roles', [])
        ->call('save')
        ->assertHasNoErrors();

    expect($teacher->user->fresh()->hasRole('teacher'))->toBeFalse();
});

it('refuses recommendation actions when the teacher role is revoked', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $component = Livewire::actingAs($teacher->user)
        ->test('pages::teacher.class-details', ['assignment' => $assignment->id]);

    $teacher->user->removeRole('teacher');
    $component->call('recommendStudent', $student->id)->assertForbidden();

    $this->assertDatabaseCount('recovery_recommendations', 0);
});

it('enforces unique recommendations and protects both referenced records in the database', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);

    expect(fn () => DB::table('recovery_recommendations')->insert([
        'teaching_assignment_id' => $assignment->id,
        'student_profile_id' => $student->id,
    ]))->toThrow(UniqueConstraintViolationException::class);
    expect(fn () => $assignment->delete())->toThrow(QueryException::class);
    expect(fn () => $student->delete())->toThrow(QueryException::class);
});

it('prevents deletion of student or teacher accounts with recommendations', function (string $account) {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);
    $user = $account === 'student' ? $student->user : $teacher->user;
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)->test('pages::users.index')
        ->call('confirmDelete', $user->id)
        ->call('delete')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Rimuovi le segnalazioni di recupero prima di eliminare questo utente.');

    $this->assertModelExists($user);
})->with(['student', 'teacher']);

it('keeps a user signed in when self-deletion is blocked by recommendations', function () {
    $teacher = recoveryTeacher();
    $schoolClass = SchoolClass::factory()->create();
    $assignment = recoveryAssignment($teacher, $schoolClass, Subject::factory()->create());
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $assignment->recommendedStudents()->attach($student);

    Livewire::actingAs($student->user)->test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasErrors(['account']);

    $this->assertModelExists($student->user);
    $this->assertAuthenticatedAs($student->user);
});
