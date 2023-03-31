<?php

namespace Demidovich\EloquentLogger;

use Illuminate\Database\Eloquent\Model;
use Demidovich\EloquentLogger\Logger;

trait HasEloquentLogger
{
    public static function booted()
    {
        $logger = new Logger;

        static::created(function (Model $model) use ($logger) {
            $logger->logCreated($model);
        });

        static::updating(function (Model $model) use ($logger)  {
            $logger->logUpdatedStart($model);
        });

        static::updated(function (Model $model) use ($logger)  {
            $logger->logUpdatedComplete($model);
        });

        static::deleted(function (Model $model) use ($logger)  {
            $logger->logDeleted($model);
        });
    }

    public function unloggableAttributes(): array
    {
        return isset($this->unloggable) ? $this->unloggable : [];
    }
}
