<?php

namespace Gyrobus\MoonshineStaticData\Models;

use Illuminate\Database\Eloquent\Model;

class StaticDataGroup extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];
}
