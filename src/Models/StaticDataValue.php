<?php

namespace Gyrobus\StaticData\Models;

use Gyrobus\StaticData\Models\StaticData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaticDataValue extends Model
{
    protected $fillable = [
        'static_data_id', 'data', 'lang'
    ];

    public function staticData(): BelongsTo
    {
        return $this->belongsTo(StaticData::class);
    }
}
