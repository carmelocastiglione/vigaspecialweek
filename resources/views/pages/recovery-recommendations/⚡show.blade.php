<?php

use App\Models\StudentProfile;
use App\Models\TeachingAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $assignmentId;

    public ?int $studentProfileToAdd = null;

    public function boot(): void
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);
    }

    public function mount(int $assignment): void
    {
        $this->assignmentId = TeachingAssignment::findOrFail($assignment)->id;
    }

    public function addStudent(): void
    {
        $assignment = TeachingAssignment::findOrFail($this->assignmentId);

        $this->validate([
            'studentProfileToAdd' => ['required', 'integer', 'exists:student_profiles,id,school_class_id,'.$assignment->school_class_id],
        ], [], ['studentProfileToAdd' => 'studente']);

        $assignment->recommendedStudents()->syncWithoutDetaching([$this->studentProfileToAdd]);
        $this->reset('studentProfileToAdd');
    }

    public function removeStudent(int $studentProfileId): void
    {
        TeachingAssignment::findOrFail($this->assignmentId)->recommendedStudents()->detach($studentProfileId);
    }

    public function with(): array
    {
        $assignment = TeachingAssignment::with(['teacherProfile.user', 'schoolClass', 'subject'])->findOrFail($this->assignmentId);

        $classStudents = StudentProfile::query()
            ->where('school_class_id', $assignment->school_class_id)
            ->with('user')
            ->get()
            ->filter(fn (StudentProfile $student) => $student->user !== null)
            ->sortBy([
                fn ($a, $b) => strcasecmp($a->user->surname, $b->user->surname),
                fn ($a, $b) => strcasecmp($a->user->name, $b->user->name),
            ]);

        $recommendedIds = $assignment->recommendedStudents()->pluck('student_profiles.id');

        return [
            'assignment' => $assignment,
            'recommendedStudents' => $classStudents->whereIn('id', $recommendedIds),
            'availableStudents' => $classStudents->whereNotIn('id', $recommendedIds),
        ];
    }
};
?>

<div class="flex flex-col gap-6">
    <div>
        <flux:link :href="route('admin.recovery-recommendations')" wire:navigate>Torna alle segnalazioni rinforzo</flux:link>
    </div>

    <div>
        <flux:heading size="xl" level="1">Studenti segnalati</flux:heading>
        <flux:subheading size="lg">
            {{ $assignment->teacherProfile?->user?->surname }} {{ $assignment->teacherProfile?->user?->name }}
            — {{ $assignment->subject?->description }} — {{ $assignment->schoolClass?->description }}
        </flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="addStudent" class="flex items-end gap-3">
            <div class="flex-1">
                <flux:select wire:model="studentProfileToAdd" wire:key="add-student-select-{{ $recommendedStudents->count() }}" label="Aggiungi studente della classe">
                    <flux:select.option value="">Seleziona uno studente</flux:select.option>
                    @foreach ($availableStudents as $student)
                        <flux:select.option value="{{ $student->id }}" wire:key="available-{{ $student->id }}">{{ $student->user->surname }} {{ $student->user->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <flux:button type="submit" variant="primary">Aggiungi</flux:button>
        </form>
    </flux:card>

    <flux:card>
        @if ($recommendedStudents->isNotEmpty())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Studente</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($recommendedStudents as $student)
                        <flux:table.row wire:key="recommended-{{ $student->id }}">
                            <flux:table.cell>{{ $student->user->surname }} {{ $student->user->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" variant="danger" wire:click="removeStudent({{ $student->id }})" wire:confirm="Rimuovere la segnalazione?">Rimuovi</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <flux:text>Nessuno studente segnalato.</flux:text>
        @endif
    </flux:card>
</div>
