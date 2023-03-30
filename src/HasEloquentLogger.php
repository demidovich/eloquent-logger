<?php

namespace Demidovich\EloquentLogger;

use Illuminate\Database\Eloquent\Model;
use Demidovich\EloquentLogger\Logger;

trait HasEloquentLogger
{
    private array $unloggable = [];

    public static function booted()
    {
        $logger = new Logger;

        static::created(function (Model $model) use ($logger) {
            $logger->storeCreated($model);
        });

        static::updating(function (Model $model) use ($logger)  {
            $logger->storeUpdatedStart($model);
        });

        static::updated(function (Model $model) use ($logger)  {
            $logger->storeUpdated($model);
        });

        static::deleted(function (Model $model) use ($logger)  {
            $logger->storeDeleted($model);
        });
    }

    public function unloggable(): array
    {
        return $this->unloggable;
    }
}
