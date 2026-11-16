<?php

use App\Models\StudentProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $assignmentId;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('teacher'), 403);
    }

    public function mount(int $assignment): void
    {
        $this->assignmentId = $assignment;
    }

    public function recommendStudent(int $studentProfileId): void
    {
        $this->updateRecommendation($studentProfileId, true);
    }

    public function removeRecommendation(int $studentProfileId): void
    {
        $this->updateRecommendation($studentProfileId, false);
    }

    private function updateRecommendation(int $studentProfileId, bool $recommend): void
    {
        DB::transaction(function () use ($studentProfileId, $recommend): void {
            $user = User::query()->lockForUpdate()->findOrFail(auth()->id());
            abort_unless($user->hasRole('teacher'), 403);

            $assignment = TeachingAssignment::query()
                ->whereHas('teacherProfile', fn (Builder $query) => $query->where('user_id', $user->id))
                ->lockForUpdate()
                ->findOrFail($this->assignmentId);

            $student = StudentProfile::query()
                ->where('school_class_id', $assignment->school_class_id)
                ->lockForUpdate()
                ->findOrFail($studentProfileId);

            if ($recommend) {
                $assignment->recommendedStudents()->syncWithoutDetaching([$student->id]);
            } else {
                $assignment->recommendedStudents()->detach($student->id);
            }
        });
    }

    public function with(): array
    {
        $assignment = TeachingAssignment::query()
            ->whereHas('teacherProfile', fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->with(['schoolClass', 'subject'])
            ->findOrFail($this->assignmentId);

        return [
            'assignment' => $assignment,
            'students' => User::query()
                ->whereHas('studentProfile', fn (Builder $query) => $query->where('school_class_id', $assignment->school_class_id))
                ->with('studentProfile')
                ->orderBy('surname')
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
            'recommendedStudentIds' => $assignment->recommendedStudents()->pluck('student_profiles.id'),
        ];
    }
};
?>

<div class="flex flex-col gap-6">
    <div>
        <flux:link :href="route('teacher.classes')" wire:navigate>Torna alle mie classi</flux:link>
    </div>

    <div>
        <flux:heading size="xl" level="1">{{ $assignment->schoolClass->description }} · {{ $assignment->subject->description }}</flux:heading>
        <flux:subheading>Studenti della classe</flux:subheading>
    </div>

    <flux:card>
        <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse ($students as $student)
                <li wire:key="student-{{ $student->id }}" class="flex items-center justify-between gap-4 py-3">
                    <flux:text>{{ $student->surname }} {{ $student->name }}</flux:text>
                    @if ($student->studentProfile)
                        @php($isRecommended = $recommendedStudentIds->contains($student->studentProfile->id))
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            wire:click="{{ $isRecommended ? 'removeRecommendation' : 'recommendStudent' }}({{ $student->studentProfile->id }})"
                            wire:loading.attr="disabled"
                            wire:target="recommendStudent({{ $student->studentProfile->id }}),removeRecommendation({{ $student->studentProfile->id }})"
                            aria-pressed="{{ $isRecommended ? 'true' : 'false' }}"
                            aria-label="{{ $isRecommended ? 'Rimuovi segnalazione di recupero per' : 'Segnala per recupero' }} {{ $student->name }} {{ $student->surname }}"
                            title="{{ $isRecommended ? 'Rimuovi segnalazione di recupero' : 'Segnala per recupero' }}"
                        >
                            <flux:icon.exclamation-triangle
                                :variant="$isRecommended ? 'solid' : 'outline'"
                                @class([
                                    'text-amber-600 dark:text-amber-400' => $isRecommended,
                                    'text-zinc-500 dark:text-zinc-400' => ! $isRecommended,
                                ])
                            />
                        </flux:button>
                    @endif
                </li>
            @empty
                <li><flux:text>Nessuno studente presente in questa classe.</flux:text></li>
            @endforelse
        </ul>
    </flux:card>
</div>