<?php

namespace Gyrobus\MoonshineStaticData\Traits;

use Gyrobus\MoonshineStaticData\Models\StaticData;
use Illuminate\Support\Facades\View;

trait StaticDataTrait {
    /**
     * Загружает статические данные по group_slug и передаёт их во View
     *
     * @param string $groupSlug
     * @param string $viewVariableName
     * @return void
     */
    public function loadStaticDataByGroup(string $groupSlug, string $viewVariableName = 'staticData'): void
    {
        $staticData = StaticData::where('group_slug', $groupSlug)->get();

        // Передаём переменную во все View
        View::share($viewVariableName, $staticData);
    }
}
