<?php

namespace Demidovich\EloquentLogger;

use Illuminate\Database\Eloquent\Model;

class Logger
{
    private array $stateUpdated = [];
    private ?string $userId = null;

    public function __construct(?string $userId = null)
    {
        $this->userId = $userId;
    }

    public function logCreated(Model $model): void
    {
        $modifiedState = $this->loggableState($model, $model->toArray());
        if (! $modifiedState) {
            return;
        }

        $record = $this->record("create", $model);
        $record->modified_state = $modifiedState;
        $record->save();
    }

    /**
     * Начало логирования обновления
     * Получение диффа изменений
     * Писать лог будем только после фактического сохранения модели по событию updated
     */
    public function logUpdatedStart(Model $model): void
    {
        $original = $model->getOriginal();
        $modified = [];

        foreach ($model->getDirty() as $attr => $value) {
            if (! isset($original[$attr]) || $original[$attr] != $value) {
                $modified[$attr] = $value;
            }
        }

        $this->stateUpdated = $this->loggableState($model, $modified);
    }

    /**
     * Завершение логирования обновления
     *
     * @param Model $model
     * @return void
     */
    public function logUpdatedComplete(Model $model): void
    {
        if (empty($this->stateUpdated)) {
            return;
        }

        $record = $this->record("update", $model);
        $record->modified_state = $this->stateUpdated;
        $record->save();
    }

    public function logDeleted(Model $model): void
    {
        $record = $this->record("delete", $model);
        $record->save();
    }

    private function record(string $operation, Model $model): ModificationLog
    {
        $record = new ModificationLog();
        $record->operation = $operation;
        $record->table     = $model->getTable();
        $record->table_id  = $model->getKey();
        $record->user_id   = $this->userId;

        return $record;
    }

    private function loggableState(Model $model, array $state): array
    {
        if (! $model->unloggableAttributes() || ! $state) {
            return $state;
        }

        return array_diff_key(
            $state,
            array_flip(
                $model->unloggableAttributes()
            )
        );
    }
}
