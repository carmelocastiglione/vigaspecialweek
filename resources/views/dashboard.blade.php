<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Card per gli amministratori --}}
        @can('admin.site')
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <x-card-dashboard 
                title="Utenti" 
                value="{{ $usersCount ?? '-' }}" 
                label="Gestisci utenti" 
                href="{{ route('users.index') }}" 
            />
            <x-card-dashboard 
                title="Dipartimenti" 
                value="{{ $departmentsCount ?? '-' }}" 
                label="Gestisci dipartimenti" 
                href="{{ route('departments.index') }}" 
            />
            <x-card-dashboard 
                title="Categorie" 
                value="{{ $categoriesCount ?? '-' }}" 
                label="Gestisci categorie" 
                href="{{ route('categories.index') }}" 
            />
            <x-card-dashboard 
                title="Materie" 
                value="{{ $subjectsCount ?? '-' }}" 
                label="Gestisci matierie" 
                href="{{ route('subjects.index') }}" 
            />
            <x-card-dashboard 
                title="Aule" 
                value="{{ $classroomsCount ?? '-' }}" 
                label="Gestisci aule" 
                href="{{ route('classrooms.index') }}" 
            />
            <x-card-dashboard 
                title="Classi" 
                value="{{ $classesCount ?? '-' }}" 
                label="Gestisci classi" 
                href="#" 
            />
        </div>
        @endcan
    </div>
</x-layouts::app>
