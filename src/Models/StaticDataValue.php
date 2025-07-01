<?php

namespace Gyrobus\MoonshineStaticData\Models;

use Gyrobus\MoonshineStaticData\Models\StaticData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaticDataValue extends Model
{
    protected $fillable = [
        'static_data_id', 'data', 'options', 'lang'
    ];

    protected $casts = [
        'options' => 'json'
    ];

    public function staticData(): BelongsTo
    {
        return $this->belongsTo(StaticData::class);
    }
}
