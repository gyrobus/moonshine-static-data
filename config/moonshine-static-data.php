<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    |
    | Languages supported by MoonShine Static Data package
    |
    */

    'languages' => [
        'ru' => 'Русский',
        'en' => 'English',
    ],

    'upload' => [

        'disk' => env('MOONSHINE_STATIC_DISK', 'public'),
        'dir' => env('MOONSHINE_STATIC_DIR', 'static'),

        'cropper' => [
            'mode' => 1
        ]

    ],

    'cache_hours' => 0,

    'has_create' => true

];
