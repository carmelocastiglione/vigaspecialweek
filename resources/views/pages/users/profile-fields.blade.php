<div class="space-y-4 border-t pt-4">
    <flux:heading size="md">Profilo</flux:heading>
    <flux:field>
        <flux:label>Tipo di profilo</flux:label>
        <flux:select wire:model.live="profile.type">
            @if (! $hasProfile)
                <option value="none">Nessun profilo</option>
            @endif
            <option value="student">Studente</option>
            <option value="teacher">Docente</option>
        </flux:select>
        <flux:error name="profile.type" />
    </flux:field>

    @if ($hasProfile && $profile->type !== $initialProfileType)
        <p class="text-sm text-red-600">Cambiando il tipo di profilo, i dati del profilo precedente saranno eliminati al salvataggio.</p>
    @endif

    @if ($profile->type === 'student')
        <flux:field>
            <flux:label>Classe *</flux:label>
            <flux:select wire:model="profile.school_class_id">
                <option value="">Seleziona una classe</option>
                @foreach ($schoolClasses as $schoolClass)
                    <option value="{{ $schoolClass->id }}" wire:key="student-class-{{ $schoolClass->id }}">{{ $schoolClass->description }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.school_class_id" />
        </flux:field>
    @elseif ($profile->type === 'teacher')
        <flux:field>
            <flux:label>Dipartimento *</flux:label>
            <flux:select wire:model="profile.department_id">
                <option value="">Seleziona un dipartimento</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" wire:key="teacher-department-{{ $department->id }}">{{ $department->description }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.department_id" />
        </flux:field>

        <flux:field>
            <flux:label>Classe coordinata</flux:label>
            <flux:select wire:model="profile.coordinator_class_id">
                <option value="">Nessuna</option>
                @foreach ($schoolClasses as $schoolClass)
                    <option value="{{ $schoolClass->id }}" wire:key="coordinator-class-{{ $schoolClass->id }}">{{ $schoolClass->description }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.coordinator_class_id" />
        </flux:field>

        <flux:field>
            <flux:label>Dipartimento di cui è responsabile</flux:label>
            <flux:select wire:model="profile.responsible_department_id">
                <option value="">Nessuno</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" wire:key="responsible-department-{{ $department->id }}">{{ $department->description }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.responsible_department_id" />
        </flux:field>

        <flux:field>
            <flux:label>Identificativo interno</flux:label>
            <flux:input wire:model="profile.internal_id" />
            <flux:error name="profile.internal_id" />
        </flux:field>

        <flux:field>
            <flux:label>Mansione</flux:label>
            <flux:select wire:model="profile.job">
                <option value="">Non specificata</option>
                @foreach (\App\Enums\TeacherJob::cases() as $job)
                    <option value="{{ $job->value }}" wire:key="job-{{ $job->value }}">{{ $job->label() }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.job" />
        </flux:field>

        <flux:field>
            <flux:label>Contratto</flux:label>
            <flux:select wire:model="profile.contract">
                <option value="">Non specificato</option>
                @foreach (\App\Enums\TeacherContract::cases() as $contract)
                    <option value="{{ $contract->value }}" wire:key="contract-{{ $contract->value }}">{{ $contract->label() }}</option>
                @endforeach
            </flux:select>
            <flux:error name="profile.contract" />
        </flux:field>

        <flux:field>
            <flux:label>Ore</flux:label>
            <flux:input wire:model="profile.hours" type="number" step="1" />
            <flux:error name="profile.hours" />
        </flux:field>

        <flux:checkbox wire:model="profile.no_first_hour" label="Non disponibile alla prima ora" />
        <flux:error name="profile.no_first_hour" />
    @endif
</div>
