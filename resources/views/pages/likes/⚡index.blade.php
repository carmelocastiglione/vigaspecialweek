<?php

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function with(): array
    {
        abort_unless(Auth::user()?->hasRole('admin'), 403);
        Gate::authorize('viewAny', Activity::class);

        $studentsCount = User::role('student')->count();
        $studentLikesCount = User::role('student')
            ->join('activity_likes', 'users.id', '=', 'activity_likes.user_id')
            ->count('activity_likes.id');

        return [
            'activities' => Activity::query()
                ->withCount('likedByUsers')
                ->orderByDesc('liked_by_users_count')
                ->orderBy('title')
                ->orderBy('id')
                ->paginate(15),
            'studentsCount' => $studentsCount,
            'studentsWithLikesCount' => User::role('student')->has('likedActivities')->count(),
            'averageLikesPerStudent' => $studentsCount > 0 ? $studentLikesCount / $studentsCount : 0,
            'activitiesWithLikesCount' => Activity::has('likedByUsers')->count(),
        ];
    }
};
?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">Like</flux:heading>
        <flux:subheading size="lg">Attività ordinate per numero di like ricevuti.</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <flux:card>
            <flux:text size="sm">Studenti con almeno un like</flux:text>
            <flux:heading size="xl" class="mt-2">{{ $studentsWithLikesCount }} / {{ $studentsCount }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:text size="sm">Media like per studente</flux:text>
            <flux:heading size="xl" class="mt-2">{{ number_format($averageLikesPerStudent, 2, ',', '.') }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:text size="sm">Attività con almeno un like</flux:text>
            <flux:heading size="xl" class="mt-2">{{ $activitiesWithLikesCount }} / {{ $activities->total() }}</flux:heading>
        </flux:card>
    </div>

    <flux:card>
        @if ($activities->isNotEmpty())
            <flux:table :paginate="$activities">
                <flux:table.columns>
                    <flux:table.column>Attività</flux:table.column>
                    <flux:table.column>Tipo</flux:table.column>
                    <flux:table.column>Like</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($activities as $activity)
                        <flux:table.row wire:key="activity-{{ $activity->id }}">
                            <flux:table.cell>{{ $activity->title }}</flux:table.cell>
                            <flux:table.cell>{{ $activity->activity_type->label() }}</flux:table.cell>
                            <flux:table.cell>{{ $activity->liked_by_users_count }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <flux:text>Nessuna attività disponibile.</flux:text>
        @endif
    </flux:card>
</div>