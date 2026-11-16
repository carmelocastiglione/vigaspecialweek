<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class UserProfileManager
{
    /** @param array<string, mixed> $attributes */
    public function save(User $user, string $type, array $attributes): void
    {
        if (! in_array($type, ['none', 'student', 'teacher'], true)) {
            throw new InvalidArgumentException('Tipo di profilo non valido.');
        }

        DB::transaction(function () use ($user, $type, $attributes): void {
            $user = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            if ($type === 'none') {
                if ($user->studentProfile()->exists() || $user->teacherProfile()->exists()) {
                    throw ValidationException::withMessages([
                        'profile.type' => 'Non è possibile rimuovere il profilo esistente.',
                    ]);
                }

                return;
            }

            $studentProfile = $user->studentProfile()->lockForUpdate()->first();
            if ($studentProfile && $studentProfile->recommendingAssignments()->exists()) {
                if ($type === 'teacher') {
                    throw ValidationException::withMessages([
                        'profile.type' => 'Rimuovi le segnalazioni di recupero prima di cambiare il tipo di profilo.',
                    ]);
                }

                if (array_key_exists('school_class_id', $attributes) && (int) $attributes['school_class_id'] !== (int) $studentProfile->school_class_id) {
                    throw ValidationException::withMessages([
                        'profile.school_class_id' => 'Rimuovi le segnalazioni di recupero prima di cambiare la classe.',
                    ]);
                }
            }

            if ($type === 'student') {
                $teacherProfile = $user->teacherProfile()->lockForUpdate()->first();
                if ($teacherProfile && $teacherProfile->teachingAssignments()->exists()) {
                    throw ValidationException::withMessages([
                        'profile.type' => 'Rimuovi le assegnazioni del docente prima di cambiare il tipo di profilo.',
                    ]);
                }

                $user->teacherProfile()->delete();
                $user->studentProfile()->updateOrCreate([], $attributes);
            } else {
                $user->studentProfile()->delete();
                $user->teacherProfile()->updateOrCreate([], $attributes);
            }
        });
    }

    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $studentProfile = $user->studentProfile()->lockForUpdate()->first();

            if ($studentProfile && $studentProfile->recommendingAssignments()->exists()) {
                throw ValidationException::withMessages([
                    'account' => 'Rimuovi le segnalazioni di recupero prima di eliminare questo utente.',
                ]);
            }

            if ($this->teacherHasRecommendations($user)) {
                throw ValidationException::withMessages([
                    'account' => 'Rimuovi le segnalazioni di recupero prima di eliminare questo utente.',
                ]);
            }

            $user->delete();
        });
    }

    public function assertTeacherRoleCanBeRemoved(User $user): void
    {
        if ($this->teacherHasRecommendations($user)) {
            throw ValidationException::withMessages([
                'roles' => 'Rimuovi le segnalazioni di recupero prima di togliere il ruolo docente.',
            ]);
        }
    }

    private function teacherHasRecommendations(User $user): bool
    {
        $teacherProfile = $user->teacherProfile()->lockForUpdate()->first();

        if (! $teacherProfile) {
            return false;
        }

        $teacherProfile->teachingAssignments()->lockForUpdate()->get(['id']);

        return $teacherProfile->teachingAssignments()->whereHas('recommendedStudents')->exists();
    }
}
