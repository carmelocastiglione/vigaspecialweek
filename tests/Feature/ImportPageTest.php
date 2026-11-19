<?php

use App\Models\Category;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\EnrichmentActivity;
use App\Models\RemedialActivity;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function importAdmin(): User
{
    test()->seed(RoleAndPermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

function importCsv(string $contents): UploadedFile
{
    return UploadedFile::fake()->createWithContent('import.csv', $contents);
}

it('restricts the page and actions to administrators', function () {
    $admin = importAdmin();
    $student = User::factory()->create();
    $student->assignRole('student');

    $this->get(route('imports.index'))->assertRedirect(route('login'));
    $this->actingAs($student)->get(route('imports.index'))->assertForbidden();
    $component = Livewire::actingAs($admin)->test('pages::imports.index');
    $this->actingAs($admin)->get(route('dashboard'))->assertSee(route('imports.index'));
    $this->actingAs($student)->get(route('dashboard'))->assertDontSee(route('imports.index'));
    $this->actingAs($student);
    $component->call('selectType', 'categories')->assertForbidden();
});

it('shows missing and existing data while keeping populated types importable', function () {
    $admin = importAdmin();
    Category::create(['internal_id' => 'AR1', 'description' => 'Arte']);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->assertSee('Categorie')
        ->assertSee('1 record presenti')
        ->assertSee('Dati presenti')
        ->assertSee('Da importare: nessun dato')
        ->assertSee('Formato CSV da definire')
        ->call('selectType', 'categories')
        ->assertSee('id_cat_corso;descrizione');
});

it('requires confirmation and prevents non-admins from emptying a table', function () {
    $admin = importAdmin();
    $student = User::factory()->create();
    $student->assignRole('student');
    $category = Category::factory()->create();
    Livewire::actingAs($admin)->test('pages::imports.index')->call('emptyTable')->assertForbidden();
    Livewire::actingAs($admin)->test('pages::imports.index')->call('confirmEmpty', 'users')->assertNotFound();
    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'categories')
        ->assertSee('Questa operazione è irreversibile.');
    $this->assertModelExists($category);

    $this->actingAs($student);
    $component->call('emptyTable')->assertForbidden();
    $this->assertModelExists($category);
});

it('deletes only unlinked categories and reports both counts', function () {
    $admin = importAdmin();
    $linked = Category::factory()->create();
    $unlinked = Category::factory()->create();
    $activity = EnrichmentActivity::factory()->create(['category_id' => $linked->id]);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'categories')
        ->call('emptyTable')
        ->assertSet('showEmptyModal', false)
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Categorie: 1 record eliminati; 1 non eliminabili perché collegati ad altri dati.');

    $this->assertModelMissing($unlinked);
    $this->assertModelExists($linked);
    $this->assertDatabaseHas('enrichment_activities', ['id' => $activity->id, 'category_id' => $linked->id]);
});

it('empties multiple batches and reports a completely successful result', function () {
    $admin = importAdmin();
    Category::factory()->count(205)->create();
    $classroom = Classroom::create([
        'description' => 'Aula 1',
        'capacity' => 20,
        'floor' => 'terra',
        'building' => 'sede centrale',
        'type' => 'aula',
    ]);

    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'categories')
        ->call('emptyTable')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Categorie: 205 record eliminati; 0 non eliminabili perché collegati ad altri dati.');

    $this->assertDatabaseCount('categories', 0);
    $this->assertModelExists($classroom);

    $component->call('confirmEmpty', 'classrooms')->call('emptyTable')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Aule: 1 record eliminati; 0 non eliminabili perché collegati ad altri dati.');
    $this->assertModelMissing($classroom);
});

it('rechecks assignment permissions before emptying the table', function () {
    $admin = importAdmin();
    $teacher = TeacherProfile::factory()->create();
    $assignment = $teacher->teachingAssignments()->create([
        'school_class_id' => SchoolClass::factory()->create()->id,
        'subject_id' => Subject::factory()->create()->id,
    ]);
    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'assignments');
    Role::findByName('admin')->revokePermissionTo('assignments.delete');

    $component->call('emptyTable')->assertForbidden();
    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'assignments')
        ->assertForbidden();
    $this->assertModelExists($assignment);
});

it('preserves departments linked to teachers, coordinators or subjects', function () {
    $admin = importAdmin();
    $teacherDepartment = Department::factory()->create();
    $coordinatedDepartment = Department::factory()->create();
    $subjectDepartment = Department::factory()->create();
    $unlinked = Department::factory()->create();
    $teacher = TeacherProfile::factory()->create([
        'department_id' => $teacherDepartment->id,
        'responsible_department_id' => $coordinatedDepartment->id,
    ]);
    $subject = Subject::factory()->create(['department_id' => $subjectDepartment->id]);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'departments')
        ->call('emptyTable')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Dipartimenti: 1 record eliminati; 3 non eliminabili perché collegati ad altri dati.');

    $this->assertModelMissing($unlinked);
    $this->assertModelExists($teacherDepartment);
    $this->assertModelExists($coordinatedDepartment);
    $this->assertModelExists($subjectDepartment);
    $this->assertDatabaseHas('teacher_profiles', ['id' => $teacher->id, 'department_id' => $teacherDepartment->id, 'responsible_department_id' => $coordinatedDepartment->id]);
    $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'department_id' => $subjectDepartment->id]);
});

it('preserves subjects linked to remedial activities', function () {
    $admin = importAdmin();
    $linked = Subject::factory()->create(['department_id' => null]);
    $unlinked = Subject::factory()->create(['department_id' => null]);
    $activity = RemedialActivity::factory()->create(['subject_id' => $linked->id]);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('confirmEmpty', 'subjects')
        ->call('emptyTable')
        ->assertDispatched('toast-show', fn ($event, $params) => $params['slots']['text'] === 'Materie: 1 record eliminati; 1 non eliminabili perché collegati ad altri dati.');

    $this->assertModelMissing($unlinked);
    $this->assertDatabaseHas('remedial_activities', ['id' => $activity->id, 'subject_id' => $linked->id]);
});

it('preserves linked profiles, classes and assignments without deleting their users or recommendations', function () {
    $admin = importAdmin();
    $schoolClass = SchoolClass::factory()->create();
    $teacher = TeacherProfile::factory()->create(['coordinator_class_id' => $schoolClass->id]);
    $student = StudentProfile::factory()->create(['school_class_id' => $schoolClass->id]);
    $subject = Subject::factory()->create(['department_id' => $teacher->department_id]);
    $assignment = $teacher->teachingAssignments()->create(['school_class_id' => $schoolClass->id, 'subject_id' => $subject->id]);
    $assignment->recommendedStudents()->attach($student);
    $freeTeacher = TeacherProfile::factory()->create();
    $freeStudent = StudentProfile::factory()->create();
    $freeClass = SchoolClass::factory()->create();
    $component = Livewire::actingAs($admin)->test('pages::imports.index');

    foreach ([
        'teachers' => 'Docenti: 1 record eliminati; 1 non eliminabili',
        'students' => 'Alunni: 1 record eliminati; 1 non eliminabili',
        'assignments' => 'Assegnazioni docente-classe-materia: 0 record eliminati; 1 non eliminabili',
        'school-classes' => 'Classi: 2 record eliminati; 1 non eliminabili',
    ] as $type => $message) {
        $component->call('confirmEmpty', $type)->call('emptyTable')
            ->assertDispatched('toast-show', fn ($event, $params) => str_starts_with($params['slots']['text'], $message));
    }

    $this->assertModelExists($teacher);
    $this->assertModelExists($student);
    $this->assertModelExists($schoolClass);
    $this->assertModelExists($assignment);
    $this->assertModelMissing($freeTeacher);
    $this->assertModelMissing($freeStudent);
    $this->assertModelMissing($freeClass);
    $this->assertDatabaseHas('users', ['id' => $freeTeacher->user_id]);
    $this->assertDatabaseHas('users', ['id' => $freeStudent->user_id]);
    $this->assertDatabaseHas('recovery_recommendations', ['teaching_assignment_id' => $assignment->id, 'student_profile_id' => $student->id]);
});

it('previews and imports valid categories, updates existing entries and reports invalid rows', function () {
    $admin = importAdmin();
    Category::create(['internal_id' => 'AR1', 'description' => 'Vecchia']);
    $file = importCsv("\xEF\xBB\xBFID_cat_corso;descrizione\nAR1;ARTE\nAR2;MUSICA\nAR3;\nAR2;DUPLICATO\n");

    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories')
        ->set('file', $file)
        ->call('checkFile')
        ->assertSee('Controllo completato con errori')
        ->assertSee('2 validi')
        ->assertSee('2 con errori')
        ->assertSee('Record 4')
        ->assertSee('Record 5');

    $this->assertDatabaseHas('categories', ['internal_id' => 'AR1', 'description' => 'Vecchia']);
    $this->assertDatabaseMissing('categories', ['internal_id' => 'AR2']);

    $component->call('import')->assertSee('Importazione completata con scarti')->assertSee('1 inseriti')->assertSee('1 aggiornati')->assertSee('2 scartati');

    $this->assertDatabaseHas('categories', ['internal_id' => 'AR1', 'description' => 'ARTE']);
    $this->assertDatabaseHas('categories', ['internal_id' => 'AR2', 'description' => 'MUSICA']);
    $this->assertDatabaseMissing('categories', ['internal_id' => 'AR3']);
});

it('rejects wrong headers and does not import before confirmation', function () {
    $admin = importAdmin();

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories')
        ->set('file', importCsv("wrong;descrizione\nAR1;Arte\n"))
        ->call('checkFile')
        ->assertHasErrors('file')
        ->assertSee('Impossibile controllare il file')
        ->assertSee('Intestazione non valida')
        ->call('import')
        ->assertForbidden();

    $this->assertDatabaseCount('categories', 0);
});

it('shows the current phase and successful outcomes throughout the import', function () {
    $admin = importAdmin();
    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories');
    $currentPhase = function () use ($component): string {
        $document = new DOMDocument;
        $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);

        return $xpath->query("//nav[@aria-label=\"Fasi dell'importazione\"][1]//li[@aria-current='step']")->item(0)->textContent;
    };

    expect($currentPhase())->toContain('Selezione')->toContain('In corso');

    $component->set('file', importCsv("ID_cat_corso;descrizione\nAR1;Arte\n"))
        ->call('checkFile')
        ->assertSee('Controllo completato senza errori');

    expect($currentPhase())->toContain('Controllo')->toContain('In corso');

    $component->call('import')->assertSee('Importazione completata con successo');

    expect($currentPhase())->toContain('Riepilogo')->toContain('In corso');
    $this->assertDatabaseHas('categories', ['internal_id' => 'AR1', 'description' => 'Arte']);
});

it('explains when no records can be imported', function () {
    $admin = importAdmin();

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories')
        ->set('file', importCsv("ID_cat_corso;descrizione\nAR1;\n"))
        ->call('checkFile')
        ->assertSee('Nessun record valido da importare')
        ->assertSee('Record 2');

    $this->assertDatabaseCount('categories', 0);
});

it('maps classroom values and rejects unknown legacy values', function () {
    $admin = importAdmin();

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'classrooms')
        ->set('file', importCsv("ID_aula;Descrizione;Capienza;Piano;Edificio;Tipo_aula\nT_01;1P;25;terra;base;N\nT_02;2P;25;terra;ignoto;N\n"))
        ->call('checkFile')
        ->assertSee('1 validi')
        ->assertSee('Record 3')
        ->call('import')
        ->assertSee('1 inseriti')
        ->assertSee('1 scartati');

    $this->assertDatabaseHas('classrooms', ['internal_id' => 'T_01', 'capacity' => 25, 'building' => 'sede centrale', 'type' => 'aula']);
    $this->assertDatabaseMissing('classrooms', ['internal_id' => 'T_02']);
});

it('resolves department coordinators and reports missing teachers', function () {
    $admin = importAdmin();
    $teacher = TeacherProfile::factory()->create(['internal_id' => 'D091']);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'departments')
        ->set('file', importCsv("ID_dipartimento;Descrizione;id_doc_coord\nDip9;SCIENZE MOTORIE;D091\nDip2;LINGUE;D999\n"))
        ->call('checkFile')
        ->assertSee('Record 3')
        ->call('import')
        ->assertSee('1 inseriti')
        ->assertSee('1 scartati');

    $department = Department::where('internal_id', 'Dip9')->firstOrFail();
    $this->assertDatabaseHas('teacher_profiles', ['id' => $teacher->id, 'responsible_department_id' => $department->id]);
    $this->assertDatabaseMissing('departments', ['internal_id' => 'Dip2']);
});

it('rechecks subject references at confirmation and skips those that no longer exist', function () {
    $admin = importAdmin();
    $department = Department::factory()->create(['internal_id' => 'Dip12']);
    $component = Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'subjects')
        ->set('file', importCsv("ID_materia;descrizione;id_dipartimento\nMATE;MATEMATICA;Dip12\n"))
        ->call('checkFile')
        ->assertSee('1 validi');

    $department->delete();
    $component->call('import')->assertSee('Nessun record importato')->assertSee('1 scartati');

    $this->assertDatabaseMissing('subjects', ['internal_id' => 'MATE']);
});

it('imports subjects linked to existing departments', function () {
    $admin = importAdmin();
    $department = Department::factory()->create(['internal_id' => 'Dip12']);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'subjects')
        ->set('file', importCsv("ID_materia;descrizione;id_dipartimento\nMATE;MATEMATICA;Dip12\n"))
        ->call('checkFile')
        ->call('import')
        ->assertSee('1 inseriti');

    $this->assertDatabaseHas('subjects', ['internal_id' => 'MATE', 'department_id' => $department->id]);
});

it('does not replace an existing department coordinator on conflict', function () {
    $admin = importAdmin();
    $department = Department::factory()->create(['internal_id' => 'Dip9']);
    $existing = TeacherProfile::factory()->create(['responsible_department_id' => $department->id]);
    $incoming = TeacherProfile::factory()->create(['internal_id' => 'D091']);

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'departments')
        ->set('file', importCsv("ID_dipartimento;Descrizione;id_doc_coord\nDip9;SCIENZE;D091\n"))
        ->call('checkFile')
        ->assertSee('già un’altra assegnazione')
        ->call('import')
        ->assertSee('1 scartati');

    $this->assertDatabaseHas('teacher_profiles', ['id' => $existing->id, 'responsible_department_id' => $department->id]);
    $this->assertDatabaseHas('teacher_profiles', ['id' => $incoming->id, 'responsible_department_id' => null]);
    $this->assertDatabaseHas('departments', ['id' => $department->id, 'description' => $department->description]);
});

it('imports one thousand valid records', function () {
    $admin = importAdmin();
    $rows = ['ID_cat_corso;descrizione'];
    for ($i = 1; $i <= 1000; $i++) {
        $rows[] = "C{$i};Categoria {$i}";
    }

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories')
        ->set('file', importCsv(implode("\n", $rows)))
        ->call('checkFile')
        ->assertSee('1000 validi')
        ->call('import')
        ->assertSee('1000 inseriti');

    $this->assertDatabaseCount('categories', 1000);
});

it('rejects files that exceed the record limit without saving data', function () {
    $admin = importAdmin();
    $rows = ['ID_cat_corso;descrizione'];
    for ($i = 1; $i <= 5001; $i++) {
        $rows[] = "C{$i};Categoria {$i}";
    }

    Livewire::actingAs($admin)->test('pages::imports.index')
        ->call('selectType', 'categories')
        ->set('file', importCsv(implode("\n", $rows)))
        ->call('checkFile')
        ->assertHasErrors('file');

    $this->assertDatabaseCount('categories', 0);
});
