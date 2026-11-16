<?php

use App\Models\Activity;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('teacher'), 403);
    }

    public function with(): array
    {
        return [
            'activities' => Activity::query()
                ->where('user_id', auth()->id())
                ->withCount('likedByUsers')
                ->orderBy('title')
                ->orderBy('id')
                ->paginate(15),
        ];
    }
};
?>

<div class="flex flex-col gap-8">
    <div>
        <flux:heading size="xl" level="1">Le mie attività</flux:heading>
        <flux:subheading>Le attività che hai proposto e i like ricevuti.</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($activities as $activity)
            <flux:card wire:key="activity-{{ $activity->id }}" class="flex h-full flex-col gap-5 rounded-2xl border-violet-200 bg-gradient-to-br from-white to-violet-50 dark:border-violet-800 dark:from-zinc-900 dark:to-violet-950">
                <flux:heading size="lg" level="2">{{ $activity->title }}</flux:heading>
                <span class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200" role="img" aria-label="{{ $activity->liked_by_users_count }} mi piace">
                    <flux:icon.heart variant="solid" class="size-5 text-red-600 dark:text-red-400" />
                    <span aria-hidden="true">{{ $activity->liked_by_users_count }}</span>
                </span>
            </flux:card>
        @empty
            <flux:text>Nessuna attività proposta.</flux:text>
        @endforelse
    </div>

    {{ $activities->links() }}
</div>