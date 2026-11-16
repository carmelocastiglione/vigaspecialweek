<?php

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Title('Assegnazioni')]
    public string $search = '';

    public bool $showAssignmentModal = false;

    #[Locked]
    public ?int $teacherId = null;

    #[Locked]
    public string $teacherName = '';

    public string $schoolClassId = '';

    public string $subjectId = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
        Gate::authorize('assignments.view');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openAssignmentModal(int $teacherId): void
    {
        Gate::authorize('assignments.create');

        $teacher = TeacherProfile::with('user')->findOrFail($teacherId);

        $this->resetValidation();
        $this->reset('schoolClassId', 'subjectId');
        $this->teacherId = $teacher->id;
        $this->teacherName = $teacher->user->name.' '.$teacher->user->surname;
        $this->showAssignmentModal = true;
    }

    public function addAssignment(): void
    {
        Gate::authorize('assignments.create');

        $validated = $this->validate([
            'teacherId' => ['required', 'integer', 'exists:teacher_profiles,id'],
            'schoolClassId' => ['required', 'integer', 'exists:school_classes,id'],
            'subjectId' => ['required', 'integer', 'exists:subjects,id'],
        ], [], [
            'schoolClassId' => 'classe',
            'subjectId' => 'materia',
            'teacherId' => 'docente',
        ]);

        $assignment = DB::transaction(function () use ($validated) {
            $teacher = TeacherProfile::query()->lockForUpdate()->findOrFail($validated['teacherId']);

            return $teacher->teachingAssignments()->firstOrCreate([
                'school_class_id' => $validated['schoolClassId'],
                'subject_id' => $validated['subjectId'],
            ]);
        });

        if (! $assignment->wasRecentlyCreated) {
            $this->addError('subjectId', 'Questa classe e materia sono già assegnate al docente.');

            return;
        }

        $this->showAssignmentModal = false;
        Flux::toast(text: 'Assegnazione aggiunta con successo.', variant: 'success');
    }

    public function removeAssignment(int $assignmentId): void
    {
        Gate::authorize('assignments.delete');

        $removed = DB::transaction(function () use ($assignmentId): bool {
            $assignment = TeachingAssignment::query()->lockForUpdate()->findOrFail($assignmentId);

            if ($assignment->recommendedStudents()->exists()) {
                return false;
            }

            $assignment->delete();

            return true;
        });

        if (! $removed) {
            Flux::toast(text: 'Rimuovi le segnalazioni di recupero prima di eliminare questa assegnazione.', variant: 'danger');

            return;
        }

        Flux::toast(text: 'Assegnazione rimossa con successo.', variant: 'success');
    }

    public function with(): array
    {
        $search = mb_strtolower(trim($this->search));

        return [
            'teachers' => User::query()
                ->where(fn (Builder $query) => $query->role('teacher')->orWhereHas('teacherProfile'))
                ->with(['teacherProfile.teachingAssignments.schoolClass', 'teacherProfile.teachingAssignments.subject'])
                ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                    $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
                }))
                ->orderBy('surname')
                ->orderBy('name')
                ->orderBy('id')
                ->paginate(15),
            'schoolClasses' => SchoolClass::orderBy('description')->get(),
            'subjects' => Subject::orderBy('description')->get(),
        ];
    }
};
?>

<div>
    <x-status-toast />

    <div class="mb-6">
        <flux:heading size="xl">Assegnazioni</flux:heading>
        <flux:subheading>Gestisci le classi e le materie assegnate ai docenti.</flux:subheading>
    </div>

    <flux:card class="mb-6">
        <flux:input wire:model.live.debounce.300ms="search" label="Cerca docente" placeholder="Nome, cognome o email..." icon="magnifying-glass" />
    </flux:card>

    <div class="space-y-4">
        @forelse ($teachers as $teacher)
            <flux:card wire:key="teacher-{{ $teacher->id }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <flux:heading size="lg">{{ $teacher->name }} {{ $teacher->surname }}</flux:heading>
                        <flux:text>{{ $teacher->email }}</flux:text>
                    </div>
                    @can('assignments.create')
                        @if ($teacher->teacherProfile)
                            <flux:button wire:click="openAssignmentModal({{ $teacher->teacherProfile->id }})" size="sm" icon="plus">
                                Aggiungi assegnazione
                            </flux:button>
                        @endif
                    @endcan
                </div>

                @if (! $teacher->teacherProfile)
                    <flux:text class="mt-4">
                        Per aggiungere assegnazioni, crea prima il <flux:link href="{{ route('users.edit', $teacher) }}">profilo docente</flux:link>.
                    </flux:text>
                @elseif ($teacher->teacherProfile->teachingAssignments->isEmpty())
                    <flux:text class="mt-4">Nessuna assegnazione.</flux:text>
                @else
                    <ul class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($teacher->teacherProfile->teachingAssignments as $assignment)
                            <li wire:key="assignment-{{ $assignment->id }}" class="flex items-center justify-between gap-4 py-2">
                                <span class="text-sm text-zinc-700 dark:text-zinc-200">
                                    {{ $assignment->schoolClass->description }} · {{ $assignment->subject->description }}
                                </span>
                                @can('assignments.delete')
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        wire:click="removeAssignment({{ $assignment->id }})"
                                        wire:confirm="Rimuovere questa assegnazione?"
                                    >
                                        Rimuovi
                                    </flux:button>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </flux:card>
        @empty
            <flux:card>
                <flux:text>Nessun docente trovato.</flux:text>
            </flux:card>
        @endforelse
    </div>

    <div class="mt-6">{{ $teachers->links() }}</div>

    @can('assignments.create')
    <flux:modal wire:model.self="showAssignmentModal">
        <form wire:submit="addAssignment" class="space-y-6">
            <div>
                <flux:heading size="lg">Aggiungi assegnazione</flux:heading>
                <flux:subheading>{{ $teacherName }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Classe</flux:label>
                <flux:select wire:model="schoolClassId">
                    <option value="">Seleziona una classe</option>
                    @foreach ($schoolClasses as $schoolClass)
                        <option value="{{ $schoolClass->id }}" wire:key="class-option-{{ $schoolClass->id }}">{{ $schoolClass->description }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="schoolClassId" />
            </flux:field>

            <flux:field>
                <flux:label>Materia</flux:label>
                <flux:select wire:model="subjectId">
                    <option value="">Seleziona una materia</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" wire:key="subject-option-{{ $subject->id }}">{{ $subject->description }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="subjectId" />
            </flux:field>
            <flux:error name="teacherId" />

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showAssignmentModal', false)">Annulla</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="addAssignment">Salva</flux:button>
            </div>
        </form>
    </flux:modal>
    @endcan
</div>