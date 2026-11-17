<?php

use App\Models\TeachingAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);

        $assignments = TeachingAssignment::query()
            ->with(['teacherProfile.user', 'schoolClass', 'subject'])
            ->withCount('recommendedStudents')
            ->get()
            ->filter(fn (TeachingAssignment $assignment) => $assignment->teacherProfile?->user !== null)
            ->sortBy([
                fn ($a, $b) => strcasecmp($a->teacherProfile->user->surname, $b->teacherProfile->user->surname),
                fn ($a, $b) => strcasecmp($a->teacherProfile->user->name, $b->teacherProfile->user->name),
                fn ($a, $b) => strcasecmp((string) $a->subject?->description, (string) $b->subject?->description),
                fn ($a, $b) => strcasecmp((string) $a->schoolClass?->description, (string) $b->schoolClass?->description),
            ]);

        return [
            'assignments' => $assignments,
            'totalCount' => $assignments->sum('recommended_students_count'),
        ];
    }
};
?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">Segnalazioni rinforzo</flux:heading>
        <flux:subheading size="lg">Totale segnalazioni: {{ $totalCount }}</flux:subheading>
    </div>

    <flux:card>
        @if ($assignments->isNotEmpty())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Docente</flux:table.column>
                    <flux:table.column>Materia</flux:table.column>
                    <flux:table.column>Classe</flux:table.column>
                    <flux:table.column>Segnalazioni</flux:table.column>
                    <flux:table.column>Azioni</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($assignments as $assignment)
                        <flux:table.row wire:key="assignment-{{ $assignment->id }}">
                            <flux:table.cell>{{ $assignment->teacherProfile->user->surname }} {{ $assignment->teacherProfile->user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $assignment->subject?->description }}</flux:table.cell>
                            <flux:table.cell>{{ $assignment->schoolClass?->description }}</flux:table.cell>
                            <flux:table.cell>{{ $assignment->recommended_students_count }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" :href="route('admin.recovery-recommendations.show', $assignment)" wire:navigate>
                                    Gestisci studenti
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <flux:text>Nessun docente disponibile.</flux:text>
        @endif
    </flux:card>

</div>
