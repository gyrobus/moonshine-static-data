<?php

use Illuminate\Support\Facades\View;
use Gyrobus\MoonshineStaticData\Models\StaticData;

if (!function_exists('staticData')) {
    /**
     * Получить значение статических данных по "groupSlug.rowSlug"
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function staticData(string $key, $default = '', $viewVariableName = 'staticData'): string
    {
        $staticData = View::shared($viewVariableName) ?? [];
        if (isset($staticData[$key])) return $staticData[$key];

        [$groupSlug, $rowSlug] = explode('.', $key . '.', 2);

        $item = StaticData::with('data', function ($q) {
            $q->where('lang', app()->getLocale())
                ->take(1);
        })
            ->where('group_slug', $groupSlug)
            ->where('slug', $rowSlug)
            ->first();

        if ($item) {
            View::share(
                $viewVariableName,
                array_merge(
                    View::shared($viewVariableName) ?? [],
                    [implode('.', [$item->group_slug, $item->slug]) => $item->data ? $item->data[0]->data : $default]
                )
            );
        }

        return $item && $item->data ? $item->data[0]->data : $default;
    }
}