<?php

use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\UserSeeder;

it('creates a profile for the seeded student', function () {
    $this->seed([
        RoleAndPermissionSeeder::class,
        DepartmentSeeder::class,
        SchoolClassSeeder::class,
        UserSeeder::class,
    ]);

    $student = User::query()->where('email', 'studente@issvigano.org')->firstOrFail();

    expect($student->studentProfile?->schoolClass->internal_id)->toBe('1A');
});

it('creates a profile for the seeded teacher', function () {
    $this->seed([
        RoleAndPermissionSeeder::class,
        DepartmentSeeder::class,
        SchoolClassSeeder::class,
        UserSeeder::class,
    ]);

    $teacher = User::query()->where('email', 'docente@issvigano.org')->firstOrFail();

    expect($teacher->teacherProfile?->department->internal_id)->toBe('INF');
});
