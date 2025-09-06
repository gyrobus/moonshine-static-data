<?php

declare(strict_types=1);

namespace Gyrobus\MoonshineStaticData\Pages;

use Gyrobus\MoonshineStaticData\Models\StaticData;
use Gyrobus\MoonshineStaticData\Resources\StaticDataResource;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\TopBar;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Text;

class GroupIndex extends Page
{
    protected string $model = StaticData::class;

    public function getTitle(): string
    {
        return __('moonshine-static-data::main.title');
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $components = [];

        if (config('moonshine-static-data.create_group')) {
            $components[] = ActionButton::make(__('moonshine-static-data::main.addGroup'), toPage(GroupForm::class, StaticDataResource::class))->success();
        }

        if (config('moonshine-static-data.create_param')) {
            $components[] = ActionButton::make(__('moonshine-static-data::main.addData'), toPage(GroupItemCreate::class, StaticDataResource::class))->primary();
        }

        $components[] = TableBuilder::make()
            ->fields([
                Text::make(__('moonshine-static-data::main.group'), 'group')
            ])
            ->cast(new ModelCaster(StaticData::class))
            ->items(StaticData::select('group')->groupBy('group')->get())
            ->buttons([
                ActionButton::make('', fn(StaticData $item) => toPage(GroupItems::class, resource: StaticDataResource::class, params: ['group' => str($item->group)->slug()->value()]))
                    ->primary()
                    ->icon('pencil')
            ]);

        return $components;
	}
}
