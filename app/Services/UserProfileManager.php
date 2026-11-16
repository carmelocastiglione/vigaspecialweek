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
}
