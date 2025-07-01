<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Gyrobus\MoonshineStaticData\Models\StaticData;

if (!function_exists('staticData')) {
    /**
     * Получить значение статических данных по "groupSlug.rowSlug"
     *
     * @param string $key
     * @param mixed $default
     * @param string $viewVariableName
     * @return mixed
     */
    function staticData(string $key, $default = '', $viewVariableName = 'staticData'): mixed
    {
        $staticData = View::shared($viewVariableName) ?? [];
        if (isset($staticData[$key])) {
            return $staticData[$key];
        }

        $cacheHours = config('moonshine-static-data.cache_hours', 0);

        if ($cacheHours && is_numeric($cacheHours) && $cacheHours > 0) {

            $cacheKey = "static_data:{$key}:" . app()->getLocale();
            $cachedValue = Cache::get($cacheKey);

            if ($cachedValue !== null) {

                View::share(
                    $viewVariableName,
                    array_merge($staticData, [$key => $cachedValue])
                );

                return $cachedValue;
            }

        }

        [$groupSlug, $rowSlug] = explode('.', $key, 2);

        $item = StaticData::with(['data' => function ($q) {
            $q->where('lang', app()->getLocale())
                ->take(1);
        }])
            ->where('group_slug', $groupSlug)
            ->where('slug', $rowSlug)
            ->first();

        $value = $default;

        if ($item && $item->data && $item->data->count()) {

            $value = $item->data->first()->data;

            if (isset($cacheKey)) {
                Cache::put($cacheKey, $value, now()->addHours($cacheHours));
            }

            View::share(
                $viewVariableName,
                array_merge(
                    $staticData,
                    [$key => $value]
                )
            );

        }

        return $value;
    }
}