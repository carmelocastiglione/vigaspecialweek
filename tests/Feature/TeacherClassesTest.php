<?php

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

function teacherForClasses(): TeacherProfile
{
    $teacher = TeacherProfile::factory()->create();
    $teacher->user->assignRole('teacher');

    return $teacher;
}

function classAssignment(TeacherProfile $teacher, SchoolClass $schoolClass, Subject $subject): TeachingAssignment
{
    return $teacher->teachingAssignments()->create([
        'school_class_id' => $schoolClass->id,
        'subject_id' => $subject->id,
    ]);
}

it('shows only the teacher assignments with the correct student counts and links', function () {
    $teacher = teacherForClasses();
    $otherTeacher = teacherForClasses();
    $firstClass = SchoolClass::factory()->create(['description' => '1A']);
    $emptyClass = SchoolClass::factory()->create(['description' => '2B']);
    $otherClass = SchoolClass::factory()->create(['description' => '3C']);
    $subject = Subject::factory()->create(['description' => 'Matematica']);
    $otherSubject = Subject::factory()->create(['description' => 'Italiano']);
    $firstAssignment = classAssignment($teacher, $firstClass, $subject);
    $secondAssignment = classAssignment($teacher, $firstClass, $otherSubject);
    $emptyAssignment = classAssignment($teacher, $emptyClass, $subject);
    $otherAssignment = classAssignment($otherTeacher, $otherClass, $subject);
    StudentProfile::factory()->count(2)->create(['school_class_id' => $firstClass->id]);
    StudentProfile::factory()->create(['school_class_id' => $otherClass->id]);

    $this->actingAs($teacher->user)->get(route('teacher.classes'))
        ->assertSee('1A')
        ->assertSee('2B')
        ->assertSee('Matematica')
        ->assertSee('Italiano')
        ->assertSee('Studenti: 2')
        ->assertSee('Studenti: 0')
        ->assertSee(route('teacher.classes.show', $firstAssignment))
        ->assertSee(route('teacher.classes.show', $secondAssignment))
        ->assertSee(route('teacher.classes.show', $emptyAssignment))
        ->assertDontSee('3C')
        ->assertDontSee(route('teacher.classes.show', $otherAssignment));

    Livewire::actingAs($teacher->user)->test('pages::teacher.classes')
        ->assertViewHas('assignments', fn ($assignments) => $assignments->firstWhere('id', $firstAssignment->id)->schoolClass->student_profiles_count === 2
            && $assignments->firstWhere('id', $secondAssignment->id)->schoolClass->student_profiles_count === 2
            && $assignments->firstWhere('id', $emptyAssignment->id)->schoolClass->student_profiles_count === 0);

    $this->get(route('dashboard'))->assertSee(route('teacher.classes'));
});

it('shows the assigned class students in surname order without students from other classes', function () {
    $teacher = teacherForClasses();
    $schoolClass = SchoolClass::factory()->create(['description' => '1A']);
    $subject = Subject::factory()->create(['description' => 'Matematica']);
    $assignment = classAssignment($teacher, $schoolClass, $subject);
    $first = User::factory()->create(['name' => 'Anna', 'surname' => 'Bianchi']);
    $second = User::factory()->create(['name' => 'Luca', 'surname' => 'Rossi']);
    $other = User::factory()->create(['name' => 'Paolo', 'surname' => 'Verdi']);
    StudentProfile::factory()->create(['user_id' => $second->id, 'school_class_id' => $schoolClass->id]);
    StudentProfile::factory()->create(['user_id' => $first->id, 'school_class_id' => $schoolClass->id]);
    StudentProfile::factory()->create(['user_id' => $other->id]);

    $this->actingAs($teacher->user)->get(route('teacher.classes.show', $assignment))
        ->assertSee('1A')
        ->assertSee('Matematica')
        ->assertSeeInOrder(['Bianchi', 'Rossi'])
        ->assertSee('Anna')
        ->assertSee('Luca')
        ->assertDontSee('Paolo')
        ->assertSee(route('teacher.classes'));
});

it('shows empty states for teachers without assignments or students', function () {
    $user = User::factory()->create();
    $user->assignRole('teacher');
    $this->actingAs($user)->get(route('teacher.classes'))->assertSee('Nessuna assegnazione');

    $teacher = teacherForClasses();
    $assignment = classAssignment($teacher, SchoolClass::factory()->create(), Subject::factory()->create());

    $this->actingAs($user)->get(route('teacher.classes.show', $assignment))->assertNotFound();
    $this->actingAs($teacher->user)->get(route('teacher.classes.show', $assignment))
        ->assertSee('Nessuno studente');
});

it('escapes student names and assignment descriptions', function () {
    $teacher = teacherForClasses();
    $schoolClass = SchoolClass::factory()->create(['description' => '<Classe>']);
    $subject = Subject::factory()->create(['description' => '<Materia>']);
    $student = User::factory()->create(['name' => '<Nome>', 'surname' => '<Cognome>']);
    StudentProfile::factory()->create(['user_id' => $student->id, 'school_class_id' => $schoolClass->id]);
    $assignment = classAssignment($teacher, $schoolClass, $subject);

    $this->actingAs($teacher->user)->get(route('teacher.classes'))
        ->assertSee('<Classe>')
        ->assertSee('<Materia>')
        ->assertDontSee('<Classe>', false)
        ->assertDontSee('<Materia>', false);

    $this->get(route('teacher.classes.show', $assignment))
        ->assertSee('<Nome>')
        ->assertSee('<Cognome>')
        ->assertDontSee('<Nome>', false)
        ->assertDontSee('<Cognome>', false);
});

it('only allows access to the current teachers own assignment, even for the same class and subject', function () {
    $teacher = teacherForClasses();
    $otherTeacher = teacherForClasses();
    $schoolClass = SchoolClass::factory()->create();
    $subject = Subject::factory()->create();
    $ownAssignment = classAssignment($teacher, $schoolClass, $subject);
    $otherAssignment = classAssignment($otherTeacher, $schoolClass, $subject);

    $this->actingAs($teacher->user)->get(route('teacher.classes.show', $ownAssignment))->assertOk();
    $this->get(route('teacher.classes.show', $otherAssignment))->assertNotFound();
    $this->get(route('teacher.classes.show', 999999))->assertNotFound();
    $this->actingAs($otherTeacher->user)->get(route('teacher.classes.show', $otherAssignment))->assertOk();
});

it('redirects guests and refuses users without the teacher role', function (string $role) {
    $teacher = teacherForClasses();
    $assignment = classAssignment($teacher, SchoolClass::factory()->create(), Subject::factory()->create());
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->get(route('teacher.classes'))->assertRedirect(route('login'));
    $this->get(route('teacher.classes.show', $assignment))->assertRedirect(route('login'));
    $this->actingAs($user)->get(route('teacher.classes'))->assertForbidden();
    $this->get(route('teacher.classes.show', $assignment))->assertForbidden();
})->with(['student', 'admin']);

it('rechecks assignment ownership and teacher role on subsequent requests', function () {
    $teacher = teacherForClasses();
    $assignment = classAssignment($teacher, SchoolClass::factory()->create(), Subject::factory()->create());

    $detail = Livewire::actingAs($teacher->user)
        ->test('pages::teacher.class-details', ['assignment' => $assignment->id]);
    $assignment->delete();
    expect(fn () => $detail->call('$refresh'))->toThrow(ModelNotFoundException::class);

    $list = Livewire::actingAs($teacher->user)->test('pages::teacher.classes');
    $teacher->user->removeRole('teacher');
    $list->call('$refresh')->assertForbidden();
});
