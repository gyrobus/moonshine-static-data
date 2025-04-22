<?php

use Illuminate\Support\Facades\View;
use Vendor\StaticData\Models\StaticData;

if (!function_exists('staticData')) {
    /**
     * Получить значение статических данных по "groupSlug.rowSlug"
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function staticData(string $key, $default = ''): string
    {
        [$groupSlug, $rowSlug] = explode('.', $key . '.', 2);

        // Получаем расшаренную переменную staticData
        $sharedData = View::shared('staticData') ?? collect();

        $match = $sharedData->firstWhere(fn($item) => $item->group_slug === $groupSlug && $item->slug === $rowSlug);

        if ($match) {
            return $match->getData();
        }

        $fallback = StaticData::where('group_slug', $groupSlug)->where('slug', $rowSlug)->first();

        return $fallback?->getData() ?? $default;
    }
}