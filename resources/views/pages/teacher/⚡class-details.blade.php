<?php

use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
                ->orderBy('surname')
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
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
                <li wire:key="student-{{ $student->id }}" class="py-3">
                    <flux:text>{{ $student->surname }} {{ $student->name }}</flux:text>
                </li>
            @empty
                <li><flux:text>Nessuno studente presente in questa classe.</flux:text></li>
            @endforelse
        </ul>
    </flux:card>
</div>