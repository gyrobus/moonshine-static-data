<?php

namespace Gyrobus\MoonshineStaticData\Traits;

use Gyrobus\MoonshineStaticData\Models\StaticData;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

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
        $staticData = StaticData::with(['data' => function ($q) {
            $q->where('lang', app()->getLocale())
                ->take(1);
        }])
            ->where('group_slug', $groupSlug)
            ->get();

        // Передаём переменную во все View
        View::share($viewVariableName, array_merge(View::shared('staticData') ?? [], $this->getStaticDataArray($staticData)));
    }

    public function loadStaticData(string|array $slug, string $viewVariableName = 'staticData'): void
    {
        $staticData = StaticData::with(['data' => function ($q) {
            $q->where('lang', app()->getLocale())
                ->take(1);
        }]);
        $staticData = $this->getStaticDataModelQuery($staticData, $slug);
        $staticData = $staticData->get();

        View::share($viewVariableName, array_merge(View::shared('staticData') ?? [], $this->getStaticDataArray($staticData)));
    }

    protected function getStaticDataModelQuery(Builder $model, string|array $slug): Builder
    {
        if (is_array($slug)) {
            foreach ($slug as $s) {
                $model = $this->getStaticDataModelQuery($model, $s);
            }
        } elseif (is_string($slug)) {
            $slug = rtrim($slug, '.');
            $exp = explode('.', $slug);
            if (count($exp) >= 2 && $exp[0] && $exp[1]) {
                $model->orWhere(function ($q) use ($exp) {
                    $q->where('group_slug', $exp[0])
                        ->where('slug', $exp[1]);
                });
            }
        }
        return $model;
    }

    protected function getStaticDataArray(Collection $items): array
    {
        $staticData = [];
        foreach ($items as $item) {
            $staticData[implode('.', [$item->group_slug, $item->slug])] = $item->data->first()?->data ?? '';
        }
        return $staticData;
    }
}
