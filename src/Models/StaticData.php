<?php

namespace Gyrobus\MoonshineStaticData\Models;

use Gyrobus\MoonshineStaticData\Models\StaticDataValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaticData extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'group', 'group_slug'
    ];

    protected $casts = [
        'extra' => 'json'
    ];

    public function data(): HasMany
    {
        return $this->hasMany(StaticDataValue::class);
    }

    public function getData($lang = null, $default = '')
    {
        return $this->data->where('lang', $lang ?? app()->getLocale())->first()->data ?? $default;
    }
}
