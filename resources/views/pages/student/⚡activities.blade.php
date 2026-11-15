<?php

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function like(Activity $activity): void
    {
        Gate::authorize('likeActivityAsStudent', $activity);

        $user = $this->currentUser();
        $now = now();

        DB::table('activity_likes')->upsert([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['activity_id', 'user_id'], ['user_id']);
    }

    public function unlike(Activity $activity): void
    {
        Gate::authorize('likeActivityAsStudent', $activity);

        $this->currentUser()->likedActivities()->detach($activity->id);
    }

    public function with(): array
    {
        Gate::authorize('browseStudentActivities', Activity::class);

        $user = $this->currentUser();

        return [
            'activities' => Activity::query()
                ->withExists([
                    'likedByUsers as liked_by_current_user' => fn (Builder $query) => $query->where('users.id', $user->id),
                ])
                ->orderBy('title')
                ->orderBy('id')
                ->paginate(15),
        ];
    }

    private function currentUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
};
?>

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" level="1">Attività</flux:heading>
        <flux:subheading size="lg">Scopri le attività ed esprimi il tuo interesse.</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($activities as $activity)
            <flux:card wire:key="activity-{{ $activity->id }}" class="flex h-full flex-col gap-5 rounded-2xl border-violet-200 bg-gradient-to-br from-white to-violet-50 shadow-sm transition hover:border-violet-300 hover:shadow-lg dark:border-violet-800 dark:from-zinc-900 dark:to-violet-950">
                <div class="flex flex-1 flex-col items-start gap-3">
                    <flux:badge size="sm" color="violet">{{ $activity->activity_type->label() }}</flux:badge>
                    <flux:heading size="lg" level="2">{{ $activity->title }}</flux:heading>
                    @if ($activity->description)
                        <flux:text>{{ $activity->description }}</flux:text>
                    @endif
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-violet-100 pt-4 dark:border-violet-800">
                    <flux:link :href="route('student.activities.show', $activity)" :aria-label="'Visualizza dettagli: ' . $activity->title" wire:navigate>
                        Visualizza dettagli
                    </flux:link>
                    <flux:button
                        type="button"
                        variant="ghost"
                        class="rounded-full"
                        wire:click.preserve-scroll="{{ $activity->liked_by_current_user ? 'unlike' : 'like' }}({{ $activity->id }})"
                        aria-label="Mi piace: {{ $activity->title }}"
                        aria-pressed="{{ $activity->liked_by_current_user ? 'true' : 'false' }}"
                        title="{{ $activity->liked_by_current_user ? 'Rimuovi like' : 'Metti like' }}"
                    >
                        <flux:icon.heart
                            :variant="$activity->liked_by_current_user ? 'solid' : 'outline'"
                            @class([
                                'text-red-600 dark:text-red-400' => $activity->liked_by_current_user,
                                'text-zinc-500 dark:text-zinc-400' => ! $activity->liked_by_current_user,
                            ])
                        />
                    </flux:button>
                </div>
            </flux:card>
        @empty
            <flux:text>Nessuna attività disponibile.</flux:text>
        @endforelse
    </div>

    {{ $activities->links() }}
</div>