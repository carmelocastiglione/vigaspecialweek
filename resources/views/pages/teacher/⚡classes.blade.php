<?php

use App\Models\TeachingAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Livewire\Component;

new class extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('teacher'), 403);
    }

    public function with(): array
    {
        return [
            'assignments' => TeachingAssignment::query()
                ->whereHas('teacherProfile', fn (Builder $query) => $query->where('user_id', auth()->id()))
                ->with([
                    'schoolClass' => fn (BelongsTo $query) => $query->withCount('studentProfiles'),
                    'subject',
                ])
                ->orderBy('id')
                ->get(),
        ];
    }
};
?>

<div class="flex flex-col gap-8">
    <div>
        <flux:heading size="xl" level="1">Le mie classi</flux:heading>
        <flux:subheading>Le classi e le materie che ti sono state assegnate.</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($assignments as $assignment)
            <a href="{{ route('teacher.classes.show', $assignment) }}" wire:navigate wire:key="assignment-{{ $assignment->id }}" class="rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                <flux:card class="flex h-full flex-col gap-3 rounded-2xl border-violet-200 bg-gradient-to-br from-white to-violet-50 shadow-sm transition hover:border-violet-300 hover:shadow-lg dark:border-violet-800 dark:from-zinc-900 dark:to-violet-950">
                    <flux:badge size="sm" color="violet" class="self-start">{{ $assignment->schoolClass->description }}</flux:badge>
                    <flux:heading size="lg" level="2">{{ $assignment->subject->description }}</flux:heading>
                    <flux:text>Studenti: {{ $assignment->schoolClass->student_profiles_count }}</flux:text>
                </flux:card>
            </a>
        @empty
            <flux:text>Nessuna assegnazione disponibile.</flux:text>
        @endforelse
    </div>
</div>