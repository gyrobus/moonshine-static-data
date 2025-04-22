<?php

declare(strict_types=1);

namespace Gyrobus\MoonshineStaticData\Pages;

use Gyrobus\MoonshineStaticData\Models\StaticData;
use Gyrobus\MoonshineStaticData\Resources\StaticDataResource;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

class GroupItems extends Page
{
    protected string $model = StaticData::class;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $group = StaticData::select('group')
            ->groupBy('group')
            ->get()
            ->firstWhere(fn ($item) => str($item->group)->slug()->value() === request('group'))?->group;

        return [
            toPage(GroupIndex::class, StaticDataResource::class) => __('moonshine-static-data::main.title'),
            '#' => $group
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $group = StaticData::select('group')
            ->groupBy('group')
            ->get()
            ->firstWhere(fn ($item) => str($item->group)->slug()->value() === request('group'))?->group;

        $data = StaticData::where('group', $group)->get();

        return [
            TableBuilder::make()
                ->fields([
                    Text::make(__('moonshine-static-data::main.name'), 'name'),
                    Select::make('Тип', 'type')
                        ->options([
                            'editor' => __('moonshine-static-data::main.editor'),
                            'image' => __('moonshine-static-data::main.image'),
                            'text' => __('moonshine-static-data::main.text'),
                            'interval' => __('moonshine-static-data::main.interval')
                        ]),
                    Text::make('Slug', 'slug'),
                ])
                ->cast(new ModelCaster(StaticData::class))
                ->items($data)
                ->buttons([
                    ActionButton::make('', fn (StaticData $item) => toPage(GroupItemForm::class, StaticDataResource::class, [$item->id]))
                        ->primary()
                        ->icon('pencil')
                ])
        ];
	}
}
