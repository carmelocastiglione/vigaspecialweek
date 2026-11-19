<?php

namespace App\Services;

use App\Enums\ClassroomBuilding;
use App\Enums\ClassroomFloor;
use App\Enums\ClassroomType;
use App\Models\Category;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CsvImportService
{
    private const DEFINITIONS = [
        'categories' => [
            'model' => Category::class,
            'headers' => ['id_cat_corso', 'descrizione'],
        ],
        'classrooms' => [
            'model' => Classroom::class,
            'headers' => ['id_aula', 'descrizione', 'capienza', 'piano', 'edificio', 'tipo_aula'],
        ],
        'departments' => [
            'model' => Department::class,
            'headers' => ['id_dipartimento', 'descrizione', 'id_doc_coord'],
        ],
        'subjects' => [
            'model' => Subject::class,
            'headers' => ['id_materia', 'descrizione', 'id_dipartimento'],
        ],
    ];

    public function headers(string $type): array
    {
        return $this->definition($type)['headers'];
    }

    /**
     * @return array{total: int, valid: int, inserted: int, updated: int, errors: array<int, string>, preview: array<int, array{record: int, id: string, description: string}>}
     */
    public function process(string $type, string $path, bool $import = false): array
    {
        $definition = $this->definition($type);
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages(['file' => 'Impossibile leggere il file CSV.']);
        }

        try {
            $header = fgetcsv($handle, null, ';', '"', '');
            $headers = is_array($header)
                ? array_map(fn ($value) => mb_strtolower(trim((string) $value)), $header)
                : [];
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            }

            if ($headers !== $definition['headers']) {
                throw ValidationException::withMessages([
                    'file' => 'Intestazione non valida. Colonne richieste, nell’ordine: '.implode(';', $definition['headers']),
                ]);
            }

            $run = function () use ($handle, $type, $definition, $import): array {
                $result = ['total' => 0, 'valid' => 0, 'inserted' => 0, 'updated' => 0, 'errors' => [], 'preview' => []];
                $seen = [];
                $record = 1;
                $batch = [];
                $departmentIds = $type === 'subjects' ? Department::query()->whereNotNull('internal_id')->pluck('id', 'internal_id')->all() : [];

                while (($values = fgetcsv($handle, null, ';', '"', '')) !== false) {
                    $record++;
                    if ($values === [null]) {
                        continue;
                    }

                    $result['total']++;
                    if ($result['total'] > 5000) {
                        throw ValidationException::withMessages(['file' => 'Il file supera il limite di 5.000 record.']);
                    }
                    if (count($values) !== count($definition['headers'])) {
                        $result['errors'][$record] = 'Numero di colonne non valido.';

                        continue;
                    }
                    if (! mb_check_encoding(implode('', $values), 'UTF-8')) {
                        $result['errors'][$record] = 'Il record non è codificato in UTF-8.';

                        continue;
                    }

                    $row = array_combine($definition['headers'], array_map(fn ($value) => trim((string) $value), $values));
                    $id = $row[$definition['headers'][0]];
                    if ($id !== '' && isset($seen[$id])) {
                        $result['errors'][$record] = 'Codice duplicato nel file: '.$id;

                        continue;
                    }
                    $seen[$id] = true;
                    [$data, $messages] = $this->validateRow($type, $row, $departmentIds);
                    if ($messages !== []) {
                        $result['errors'][$record] = implode(' ', $messages);

                        continue;
                    }

                    $result['valid']++;
                    if (count($result['preview']) < 10) {
                        $result['preview'][] = ['record' => $record, 'id' => $id, 'description' => $row['descrizione']];
                    }

                    if ($import) {
                        if ($type === 'departments') {
                            $model = Department::query()->updateOrCreate(['internal_id' => $id], $data);
                            $result[$model->wasRecentlyCreated ? 'inserted' : 'updated']++;
                            if ($row['id_doc_coord'] !== '') {
                                TeacherProfile::query()->where('internal_id', $row['id_doc_coord'])
                                    ->firstOrFail()->update(['responsible_department_id' => $model->getKey()]);
                            }
                        } else {
                            $batch[] = ['internal_id' => $id] + $data;
                            if (count($batch) === 200) {
                                $this->saveBatch($definition['model'], $batch, $result);
                                $batch = [];
                            }
                        }
                    }
                }

                if ($batch !== []) {
                    $this->saveBatch($definition['model'], $batch, $result);
                }

                return $result;
            };

            return $import ? DB::transaction($run) : $run();
        } finally {
            fclose($handle);
        }
    }

    private function saveBatch(string $model, array $batch, array &$result): void
    {
        $existing = $model::query()->whereIn('internal_id', array_column($batch, 'internal_id'))->count();
        $result['updated'] += $existing;
        $result['inserted'] += count($batch) - $existing;
        $model::query()->upsert($batch, ['internal_id'], array_keys($batch[0]));
    }

    private function definition(string $type): array
    {
        if (! isset(self::DEFINITIONS[$type])) {
            throw ValidationException::withMessages(['file' => 'Tipo di importazione non disponibile.']);
        }

        return self::DEFINITIONS[$type];
    }

    /**
     * @return array{array<string, mixed>, array<int, string>}
     */
    private function validateRow(string $type, array $row, array $departmentIds): array
    {
        $idColumn = self::DEFINITIONS[$type]['headers'][0];
        $rules = [
            $idColumn => ['required', 'string', 'max:10'],
            'descrizione' => ['required', 'string', 'max:255'],
        ];

        if ($type === 'classrooms') {
            $row['edificio'] = $row['edificio'] === 'base' ? ClassroomBuilding::SEDE_CENTRALE->value : $row['edificio'];
            $row['tipo_aula'] = $row['tipo_aula'] === 'N' ? ClassroomType::AULA->value : $row['tipo_aula'];
            $rules += [
                'capienza' => ['required', 'integer', 'min:1', 'max:2147483647'],
                'piano' => ['required', Rule::enum(ClassroomFloor::class)],
                'edificio' => ['required', Rule::enum(ClassroomBuilding::class)],
                'tipo_aula' => ['required', Rule::enum(ClassroomType::class)],
            ];
        }
        if ($type === 'subjects') {
            $rules['id_dipartimento'] = ['required', 'string', 'max:10'];
        }
        if ($type === 'departments') {
            $rules['id_doc_coord'] = ['nullable', 'string', Rule::exists('teacher_profiles', 'internal_id')];
        }

        $validator = Validator::make($row, $rules);
        if ($validator->fails()) {
            return [[], $validator->errors()->all()];
        }

        $data = ['description' => $row['descrizione']];
        if ($type === 'classrooms') {
            $data += [
                'capacity' => (int) $row['capienza'],
                'floor' => $row['piano'],
                'building' => $row['edificio'],
                'type' => $row['tipo_aula'],
            ];
        } elseif ($type === 'subjects') {
            if (! isset($departmentIds[$row['id_dipartimento']])) {
                return [[], ['Il dipartimento indicato non esiste.']];
            }
            $data['department_id'] = $departmentIds[$row['id_dipartimento']];
        } elseif ($type === 'departments' && $row['id_doc_coord'] !== '') {
            $teacher = TeacherProfile::query()->where('internal_id', $row['id_doc_coord'])->firstOrFail();
            $department = Department::query()->where('internal_id', $row[$idColumn])->first();
            $otherResponsible = $department && TeacherProfile::query()
                ->where('responsible_department_id', $department->getKey())
                ->whereKeyNot($teacher->getKey())->exists();

            if ($otherResponsible || ($teacher->responsible_department_id !== null && $teacher->responsible_department_id !== $department?->getKey())) {
                return [[], ['Il coordinatore o il dipartimento ha già un’altra assegnazione: risolvere il conflitto prima dell’importazione.']];
            }
        }

        return [$data, []];
    }
}
