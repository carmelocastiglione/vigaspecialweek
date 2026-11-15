<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component
{
    public Activity $activity;

    public function mount(Activity $activity): void
    {
        Gate::authorize('browseStudentActivities', Activity::class);

        $this->activity = $activity;
    }

    public function with(): array
    {
        Gate::authorize('browseStudentActivities', Activity::class);

        return [];
    }
};
?>

<div class="flex flex-col gap-6">
    <div>
        <flux:link :href="route('student.activities')" wire:navigate>
            Torna alle attività
        </flux:link>
    </div>

    <flux:card class="flex flex-col items-start gap-5 rounded-2xl border-violet-200 bg-gradient-to-br from-white to-violet-50 dark:border-violet-800 dark:from-zinc-900 dark:to-violet-950">
        <flux:badge size="sm" color="violet">{{ $activity->activity_type->label() }}</flux:badge>
        <flux:heading size="xl" level="1">{{ $activity->title }}</flux:heading>

        <div class="flex flex-col gap-2">
            <flux:heading level="2">Descrizione</flux:heading>
            <flux:text class="whitespace-pre-line">{{ $activity->description ?: 'Nessuna descrizione disponibile.' }}</flux:text>
        </div>
    </flux:card>
</div>