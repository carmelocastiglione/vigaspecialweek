<?php

namespace App\Livewire\Forms;

use App\Enums\TeacherContract;
use App\Enums\TeacherJob;
use App\Models\User;
use App\Services\UserProfileManager;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class UserProfileForm extends Form
{
    public string $type = 'none';

    public ?string $school_class_id = null;

    public ?string $department_id = null;

    public ?string $coordinator_class_id = null;

    public ?string $responsible_department_id = null;

    public ?string $internal_id = null;

    public ?string $job = null;

    public ?string $contract = null;

    public bool $no_first_hour = false;

    public ?string $hours = null;

    public function setUser(User $user): void
    {
        $user->load(['studentProfile', 'teacherProfile']);

        if ($user->studentProfile && $user->teacherProfile) {
            throw ValidationException::withMessages([
                'profile.type' => 'Questo utente ha già due profili. Correggi i dati prima di modificarlo.',
            ]);
        }

        if ($profile = $user->studentProfile) {
            $this->type = 'student';
            $this->school_class_id = (string) $profile->school_class_id;
        } elseif ($profile = $user->teacherProfile) {
            $this->type = 'teacher';
            $this->department_id = (string) $profile->department_id;
            $this->coordinator_class_id = $profile->coordinator_class_id === null ? null : (string) $profile->coordinator_class_id;
            $this->responsible_department_id = $profile->responsible_department_id === null ? null : (string) $profile->responsible_department_id;
            $this->internal_id = $profile->internal_id;
            $this->job = $profile->job?->value;
            $this->contract = $profile->contract?->value;
            $this->no_first_hour = $profile->no_first_hour;
            $this->hours = $profile->hours === null ? null : (string) $profile->hours;
        }
    }

    /** @return array<string, mixed> */
    public function validateProfile(?User $user = null): array
    {
        $rules = [
            'type' => ['required', Rule::in(['none', 'student', 'teacher'])],
        ];

        if ($this->type === 'student') {
            $rules['school_class_id'] = ['required', 'integer', 'exists:school_classes,id'];
        } elseif ($this->type === 'teacher') {
            $rules += [
                'department_id' => ['required', 'integer', 'exists:departments,id'],
                'coordinator_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
                'responsible_department_id' => ['nullable', 'integer', 'exists:departments,id'],
                'internal_id' => ['nullable', 'string', 'max:255', Rule::unique('teacher_profiles', 'internal_id')->ignore($user?->getKey(), 'user_id')],
                'job' => ['nullable', Rule::enum(TeacherJob::class)],
                'contract' => ['nullable', Rule::enum(TeacherContract::class)],
                'no_first_hour' => ['boolean'],
                'hours' => ['nullable', 'integer'],
            ];
        }

        return $this->validate($rules);
    }

    /** @param array<string, mixed> $data */
    public function saveFor(User $user, array $data): void
    {
        $attributes = array_map(
            fn ($value) => $value === '' ? null : $value,
            Arr::except($data, 'type'),
        );

        app(UserProfileManager::class)->save($user, $data['type'], $attributes);
    }
}
