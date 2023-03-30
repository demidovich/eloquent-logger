<?php

namespace Demidovich\EloquentLogger;

use Illuminate\Database\Eloquent\Model;

class Logger
{
    private array $state = [];
    private ?string $userId = null;

    public function __construct(?string $userId = null)
    {
        $this->userId = $userId;
    }

    public function storeCreated(Model $model): void
    {
        $modifiedState = $this->modifiedState($model, $model->toArray());
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
    public function storeUpdatedStart(Model $model): void
    {
        $original = $model->getOriginal();
        $modified = [];

        foreach ($model->getDirty() as $attr => $value) {
            if (! isset($original[$attr]) || $original[$attr] != $value) {
                $modified[$attr] = $value;
            }
        }

        $this->state[$this->stateKey($model)] = $this->modifiedState($model, $modified);
    }

    /**
     * Завершение логирования обновления
     *
     * @param Model $model
     * @return void
     */
    public function storeUpdated(Model $model): void
    {
        $key = $this->stateKey($model);
        if (empty($this->state[$key])) {
            return;
        }

        $record = $this->record("update", $model);
        $record->modified_state = $this->modifiedState($model);
        $record->save();

        $this->state = [];
    }

    public function storeDeleted(Model $model): void
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

    private function modifiedState(Model $model, array $state): array
    {
        if (! $state) {
            return $state;
        }

        $unloggable = $model->unloggable();
        if ($unloggable) {
            $state = array_diff_key($state, array_flip($unloggable));
        }

        return $state;
    }
}
