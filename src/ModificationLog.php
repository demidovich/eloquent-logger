<?php

namespace Demidovich\EloquentLogger;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $table
 * @property string $table_id
 * @property string $operation
 * @property int    $user_id
 * @property array  $modified_state
 * @property string $created_at
 */
class ModificationLog extends Model
{
    protected $table = "modification_log";
    protected $primaryKey = "id";
    public    $timestamps = false;

    protected $casts = [
        "modified_state" => "array",
    ];

    public function setModifiedStateAttribute($value)
    {
        $this->attributes["modified_state"] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
