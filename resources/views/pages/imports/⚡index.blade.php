<?php

use App\Models\Category;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Services\CsvImportService;
use App\Services\TablePurgeService;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    #[Title('Importazioni')]
    public $file = null;

    #[Locked]
    public ?string $type = null;

    #[Locked]
    public string $step = 'selection';

    #[Locked]
    public ?string $fileHash = null;

    #[Locked]
    public array $result = [];

    #[Locked]
    public ?string $tableToEmpty = null;

    public bool $showEmptyModal = false;

    private const TYPES = [
        'categories' => ['label' => 'Categorie', 'model' => Category::class, 'available' => true],
        'classrooms' => ['label' => 'Aule', 'model' => Classroom::class, 'available' => true],
        'departments' => ['label' => 'Dipartimenti', 'model' => Department::class, 'available' => true],
        'subjects' => ['label' => 'Materie', 'model' => Subject::class, 'available' => true],
        'school-classes' => ['label' => 'Classi', 'model' => SchoolClass::class, 'available' => false],
        'teachers' => ['label' => 'Docenti', 'model' => TeacherProfile::class, 'available' => false],
        'students' => ['label' => 'Alunni', 'model' => StudentProfile::class, 'available' => false],
        'assignments' => ['label' => 'Assegnazioni docente-classe-materia', 'model' => TeachingAssignment::class, 'available' => false],
    ];

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function selectType(string $type): void
    {
        abort_unless(isset(self::TYPES[$type]) && self::TYPES[$type]['available'], 404);
        $this->reset('file', 'fileHash', 'result');
        $this->resetValidation();
        $this->type = $type;
        $this->step = 'upload';
    }

    public function updatedFile(): void
    {
        $this->reset('fileHash', 'result');
        $this->resetValidation();
        if ($this->type !== null) {
            $this->step = 'upload';
        }
    }

    public function checkFile(CsvImportService $importer): void
    {
        abort_unless($this->type !== null && $this->step === 'upload', 403);
        $this->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv', 'max:2048']]);
        $this->result = $importer->process($this->type, $this->file->getRealPath());
        $this->fileHash = hash_file('sha256', $this->file->getRealPath());
        $this->step = 'review';
    }

    public function import(CsvImportService $importer): void
    {
        abort_unless($this->type !== null && $this->step === 'review' && $this->fileHash !== null, 403);
        $this->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'extensions:csv', 'max:2048']]);

        if (! hash_equals($this->fileHash, hash_file('sha256', $this->file->getRealPath()))) {
            $this->addError('file', 'Il file è cambiato: ripeti il controllo prima di importare.');
            $this->step = 'upload';

            return;
        }

        $this->result = $importer->process($this->type, $this->file->getRealPath(), true);
        $this->step = 'summary';
        $this->file = null;
        $this->fileHash = null;
    }

    public function backToList(): void
    {
        $this->reset('file', 'fileHash', 'result', 'type');
        $this->resetValidation();
        $this->step = 'selection';
    }

    public function confirmEmpty(string $type): void
    {
        abort_unless(isset(self::TYPES[$type]) && $this->step === 'selection', 404);
        if ($type === 'assignments') {
            Gate::authorize('assignments.view');
            Gate::authorize('assignments.delete');
        }

        $this->tableToEmpty = $type;
        $this->showEmptyModal = true;
    }

    public function emptyTable(TablePurgeService $purger): void
    {
        abort_unless($this->showEmptyModal && $this->tableToEmpty !== null && isset(self::TYPES[$this->tableToEmpty]) && $this->step === 'selection', 403);

        if ($this->tableToEmpty === 'assignments') {
            Gate::authorize('assignments.view');
            Gate::authorize('assignments.delete');
        }

        $type = self::TYPES[$this->tableToEmpty];
        $result = $purger->purge(new $type['model']);
        $this->showEmptyModal = false;
        $this->tableToEmpty = null;

        Flux::toast(
            text: "{$type['label']}: {$result['deleted']} record eliminati; {$result['blocked']} non eliminabili perché collegati ad altri dati.",
            variant: $result['blocked'] > 0 ? 'warning' : 'success',
        );
    }

    public function with(CsvImportService $importer): array
    {
        $types = [];
        foreach (self::TYPES as $key => $type) {
            $types[$key] = $type + ['count' => $type['model']::query()->count()];
        }

        return [
            'types' => $types,
            'headers' => $this->type !== null ? $importer->headers($this->type) : [],
        ];
    }
};
?>

<div>
    <div class="mb-6">
        <flux:heading size="xl">Importazioni</flux:heading>
        <flux:subheading>Controlla i dati presenti e importa o aggiorna i record da file CSV.</flux:subheading>
    </div>

    @if ($step === 'selection')
        <flux:text class="mb-4">La presenza di dati non garantisce che l'importazione sia completa. Puoi importare nuovamente anche le tabelle già popolate.</flux:text>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($types as $key => $item)
                <flux:card wire:key="import-type-{{ $key }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <flux:heading size="lg">{{ $item['label'] }}</flux:heading>
                            <flux:text>{{ $item['count'] }} record presenti</flux:text>
                            <div class="mt-2">
                                @if ($item['count'] === 0)
                                    <flux:badge color="amber">Da importare: nessun dato</flux:badge>
                                @else
                                    <flux:badge color="green">Dati presenti</flux:badge>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($item['available'])
                                <flux:button wire:click="selectType('{{ $key }}')">Importa</flux:button>
                            @else
                                <flux:badge>Formato CSV da definire</flux:badge>
                            @endif
                            @if ($key !== 'assignments' || (auth()->user()->can('assignments.view') && auth()->user()->can('assignments.delete')))
                                <flux:button variant="danger" wire:click="confirmEmpty('{{ $key }}')" :disabled="$item['count'] === 0">Svuota</flux:button>
                            @endif
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <flux:modal wire:model.self="showEmptyModal">
            <div class="space-y-5">
                <flux:heading size="lg">Svuota {{ $tableToEmpty !== null ? $types[$tableToEmpty]['label'] : '' }}?</flux:heading>
                <flux:text>Questa operazione è irreversibile. Verranno eliminati solo i record senza dati collegati; gli altri saranno conservati.</flux:text>
                @if (in_array($tableToEmpty, ['teachers', 'students'], true))
                    <flux:text>Verranno eliminati i profili, non gli account utente associati.</flux:text>
                @endif
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('showEmptyModal', false)" variant="ghost">Annulla</flux:button>
                    <flux:button wire:click="emptyTable" wire:loading.attr="disabled" wire:target="emptyTable" variant="danger">Conferma svuotamento</flux:button>
                </div>
            </div>
        </flux:modal>
    @else
        <flux:button wire:click="backToList" variant="ghost" class="mb-4">Tutte le importazioni</flux:button>
        <flux:card class="space-y-5">
            <div>
                <flux:heading size="lg">{{ $types[$type]['label'] }}</flux:heading>
            </div>

            @php
                $steps = ['Selezione', 'Controllo', 'Importazione', 'Riepilogo'];
                $currentStep = match ($step) {
                    'upload' => 1,
                    'review' => 2,
                    'summary' => 4,
                };
            @endphp
            @foreach ([false, true] as $importing)
                @if (! $importing || $step === 'review')
                    <nav aria-label="Fasi dell'importazione"
                        @if ($importing) wire:loading wire:target="import" @else wire:loading.remove wire:target="import" @endif>
                        @php($activeStep = $importing ? 3 : $currentStep)
                        <ol class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($steps as $index => $label)
                                @php($number = $index + 1)
                                <li @if ($number === $activeStep) aria-current="step" @endif
                                    @class([
                                        'flex items-center gap-3 rounded-lg border p-3',
                                        'border-purple-500 bg-purple-50 text-purple-900 dark:border-pink-500 dark:bg-pink-950/30 dark:text-pink-100' => $number === $activeStep,
                                        'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-950/30 dark:text-green-200' => $number < $activeStep,
                                        'border-zinc-200 text-zinc-500 dark:border-zinc-700 dark:text-zinc-400' => $number > $activeStep,
                                    ])>
                                    <span @class([
                                        'flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold',
                                        'bg-purple-600 text-white dark:bg-pink-600' => $number === $activeStep,
                                        'bg-green-600 text-white' => $number < $activeStep,
                                        'bg-zinc-100 dark:bg-zinc-800' => $number > $activeStep,
                                    ])>{{ $number < $activeStep ? '✓' : $number }}</span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold">{{ $label }}</span>
                                        <span class="block text-xs">{{ $number === $activeStep ? 'In corso' : ($number < $activeStep ? 'Completata' : 'Da fare') }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif
            @endforeach

            @if ($step === 'upload')
                <flux:text>CSV UTF-8 separato da punto e virgola, massimo 2 MB e 5.000 record. Intestazioni, nell'ordine:</flux:text>
                <code class="block overflow-x-auto rounded bg-zinc-100 p-3 text-sm dark:bg-zinc-800">{{ implode(';', $headers) }}</code>
                @if ($type === 'departments')
                    <flux:text>Il coordinatore deve già esistere come docente. Lascia il codice vuoto per importare prima i dipartimenti; un valore vuoto non modifica eventuali coordinatori esistenti.</flux:text>
                @elseif ($type === 'subjects')
                    <flux:text>Importa prima i dipartimenti: il loro codice deve corrispondere a id_dipartimento.</flux:text>
                @elseif ($type === 'classrooms')
                    <flux:text>Per gli edifici, base corrisponde a sede centrale; per il tipo di aula, N corrisponde ad aula. Gli altri valori devono corrispondere a quelli dell'applicazione.</flux:text>
                @endif
                <form wire:submit="checkFile" class="space-y-4">
                    <flux:input type="file" wire:model="file" accept=".csv,text/csv" label="File CSV" />
                    @error('file')
                        <flux:callout variant="danger" icon="x-circle" heading="Impossibile controllare il file" role="alert">
                            <flux:callout.text>{{ $message }}</flux:callout.text>
                        </flux:callout>
                    @enderror
                    <flux:text wire:loading wire:target="file">Caricamento del file...</flux:text>
                    <flux:text wire:loading wire:target="checkFile">Controllo del file in corso...</flux:text>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="file,checkFile">Controlla il file</flux:button>
                </form>
            @elseif ($step === 'review' || $step === 'summary')
                @if ($step === 'review')
                    @if ($result['valid'] === 0)
                        <flux:callout variant="danger" icon="x-circle" heading="Nessun record valido da importare" role="alert">
                            <flux:callout.text>Correggi il file e ripeti il controllo prima di procedere.</flux:callout.text>
                        </flux:callout>
                    @elseif (count($result['errors']) > 0)
                        <flux:callout variant="warning" icon="exclamation-triangle" heading="Controllo completato con errori" role="status">
                            <flux:callout.text>Puoi importare i record validi; quelli con errori saranno scartati.</flux:callout.text>
                        </flux:callout>
                    @else
                        <flux:callout variant="success" icon="check-circle" heading="Controllo completato senza errori" role="status">
                            <flux:callout.text>Il file è pronto per l'importazione.</flux:callout.text>
                        </flux:callout>
                    @endif
                    <flux:text>{{ $result['total'] }} record esaminati; {{ $result['valid'] }} validi; {{ count($result['errors']) }} con errori. I record già presenti saranno aggiornati in base al codice.</flux:text>
                @else
                    @if ($result['inserted'] + $result['updated'] === 0)
                        <flux:callout variant="danger" icon="x-circle" heading="Nessun record importato" role="alert">
                            <flux:callout.text>Controlla gli errori riportati qui sotto e riprova con un file corretto.</flux:callout.text>
                        </flux:callout>
                    @elseif (count($result['errors']) > 0)
                        <flux:callout variant="warning" icon="exclamation-triangle" heading="Importazione completata con scarti" role="status">
                            <flux:callout.text>I record validi sono stati importati. Controlla gli errori riportati qui sotto.</flux:callout.text>
                        </flux:callout>
                    @else
                        <flux:callout variant="success" icon="check-circle" heading="Importazione completata con successo" role="status">
                            <flux:callout.text>Tutti i record validi sono stati importati.</flux:callout.text>
                        </flux:callout>
                    @endif
                    <flux:text>{{ $result['inserted'] }} inseriti; {{ $result['updated'] }} aggiornati; {{ count($result['errors']) }} scartati.</flux:text>
                @endif
                @if (count($result['preview']) > 0 && $step === 'review')
                    <div>
                        <flux:heading size="sm">Anteprima dei primi record validi</flux:heading>
                        <ul class="mt-2 space-y-1">
                            @foreach ($result['preview'] as $row)
                                <li wire:key="preview-{{ $row['record'] }}">{{ $row['id'] }} — {{ $row['description'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (count($result['errors']) > 0)
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/30">
                        <h3 class="text-sm font-semibold text-red-900 dark:text-red-200">Record con errori ({{ count($result['errors']) }})</h3>
                        <ul class="mt-2 max-h-80 space-y-2 overflow-y-auto text-sm text-red-800 dark:text-red-300">
                            @foreach ($result['errors'] as $record => $message)
                                <li wire:key="error-{{ $record }}"><strong>Record {{ $record }}:</strong> {{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if ($step === 'review')
                    <flux:text>Verranno importati solo i record validi. I dati sono ricontrollati al momento della conferma.</flux:text>
                    @error('file')
                        <flux:callout variant="danger" icon="x-circle" heading="Impossibile importare il file" role="alert">
                            <flux:callout.text>{{ $message }}</flux:callout.text>
                        </flux:callout>
                    @enderror
                    <flux:text wire:loading wire:target="import" role="status">Importazione in corso, attendi...</flux:text>
                    <div class="flex flex-wrap gap-3">
                        <flux:button wire:click="selectType('{{ $type }}')">Scegli un altro file</flux:button>
                        <flux:button wire:click="import" variant="primary" :disabled="$result['valid'] === 0" wire:loading.attr="disabled" wire:target="import">Importa i record validi</flux:button>
                    </div>
                @else
                    <flux:button wire:click="backToList" variant="primary">Torna alle importazioni</flux:button>
                @endif
            @endif
        </flux:card>
    @endif
</div>
