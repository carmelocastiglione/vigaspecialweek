<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class TablePurgeService
{
    /** @return array{deleted: int, blocked: int} */
    public function purge(Model $model): array
    {
        $connection = $model->getConnection();
        $schema = $connection->getSchemaBuilder();
        $targetTable = $connection->getTablePrefix().$model->getTable();
        $targetSchema = $connection->getDriverName() === 'sqlite' ? 'main' : $schema->getCurrentSchemaName();
        $references = [];

        foreach ($schema->getTables() as $table) {
            foreach ($schema->getForeignKeys($table['schema_qualified_name']) as $foreignKey) {
                if ($foreignKey['foreign_table'] !== $targetTable || $foreignKey['foreign_schema'] !== $targetSchema) {
                    continue;
                }

                if ($foreignKey['foreign_columns'] !== [$model->getKeyName()] || count($foreignKey['columns']) !== 1) {
                    throw new RuntimeException('Impossibile svuotare la tabella: è presente una chiave esterna non supportata.');
                }

                $references[] = [$table['schema_qualified_name'], $foreignKey['columns'][0]];
            }
        }

        return $connection->transaction(function () use ($model, $connection, $references): array {
            $deleted = 0;
            $blocked = 0;
            $lastId = 0;

            do {
                $ids = $model->newQuery()
                    ->where($model->getKeyName(), '>', $lastId)
                    ->orderBy($model->getKeyName())
                    ->limit(200)
                    ->lockForUpdate()
                    ->pluck($model->getKeyName())
                    ->all();

                if ($ids === []) {
                    break;
                }

                $lastId = end($ids);
                $referencedIds = [];
                foreach ($references as [$table, $column]) {
                    $referencedIds = array_merge(
                        $referencedIds,
                        $connection->table($table)->whereIn($column, $ids)->distinct()->pluck($column)->all()
                    );
                }

                $deletableIds = array_values(array_diff($ids, $referencedIds));
                $blocked += count($ids) - count($deletableIds);

                if ($deletableIds !== []) {
                    $deleted += $model->newQuery()->whereKey($deletableIds)->delete();
                }
            } while (count($ids) === 200);

            return ['deleted' => $deleted, 'blocked' => $blocked];
        });
    }
}
