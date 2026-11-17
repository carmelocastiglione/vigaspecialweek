<?php

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function recoveryAdmin(): User
{
    Role::findOrCreate('admin');
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

function recoveryAssignment(): TeachingAssignment
{
    return TeachingAssignment::forceCreate([
        'teacher_profile_id' => TeacherProfile::factory()->create()->id,
        'school_class_id' => SchoolClass::factory()->create()->id,
        'subject_id' => Subject::factory()->create()->id,
    ]);
}

it('shows recovery recommendations page to admin', function () {
    $this->actingAs(recoveryAdmin())->get(route('admin.recovery-recommendations'))
        ->assertOk()->assertSee('Segnalazioni rinforzo');
});

it('adds and removes students of the assignment class', function () {
    $assignment = recoveryAssignment();
    $student = StudentProfile::factory()->create(['school_class_id' => $assignment->school_class_id]);

    $this->actingAs(recoveryAdmin());
    Livewire::test('pages::recovery-recommendations.show', ['assignment' => $assignment->id])
        ->set('studentProfileToAdd', $student->id)
        ->call('addStudent')
        ->assertHasNoErrors();
    expect($assignment->recommendedStudents()->count())->toBe(1);

    Livewire::test('pages::recovery-recommendations.show', ['assignment' => $assignment->id])
        ->call('removeStudent', $student->id);
    expect($assignment->recommendedStudents()->count())->toBe(0);
});

it('rejects students from other classes', function () {
    $assignment = recoveryAssignment();
    $other = StudentProfile::factory()->create();

    $this->actingAs(recoveryAdmin());
    Livewire::test('pages::recovery-recommendations.show', ['assignment' => $assignment->id])
        ->set('studentProfileToAdd', $other->id)
        ->call('addStudent')
        ->assertHasErrors('studentProfileToAdd');
    expect($assignment->recommendedStudents()->count())->toBe(0);
});
