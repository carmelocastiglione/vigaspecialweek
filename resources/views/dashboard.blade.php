<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Card per gli amministratori --}}
        @can('admin site')
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-card-dashboard 
                title="Utenti" 
                value="{{ $usersCount ?? '-' }}" 
                label="Gestisci utenti" 
                href="/users" 
            />
            <x-card-dashboard 
                title="Aule" 
                value="{{ $classroomsCount ?? '-' }}" 
                label="Gestisci aule" 
                href="/classrooms" 
            />
            <x-card-dashboard 
                title="Classi" 
                value="{{ $classesCount ?? '-' }}" 
                label="Gestisci classi" 
                href="/classes" 
            />
        </div>
        @endcan
    </div>
</x-layouts::app>
