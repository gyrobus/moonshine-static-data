<?php

declare(strict_types=1);

namespace Gyrobus\StaticData\Pages;

use Gyrobus\StaticData\Models\StaticData;
use Gyrobus\StaticData\Resources\StaticDataResource;
use Gyrobus\MoonshineCropper\Fields\Cropper;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

class GroupItemForm extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $item = $this->getResource()->getItem();

        return [
            toPage(GroupIndex::class, StaticDataResource::class) => __('moonshine-static-data::main.title'),
            toPage(GroupItems::class, StaticDataResource::class, ['group' => str($item->group)->slug()->value()]) => $item->group,
            '#' => $item->name
        ];
    }

    public function getTitle(): string
    {
        $item = $this->getResource()->getItem();
        return __('moonshine-static-data::main.editParam', ['name' => $item->name]);
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $item = $this->getResource()->getItem();
        $data = $item->data->keyBy('lang');
        $languages = config('moonshine-static-data.languages');
        $tabs = [];

        foreach ($languages as $lang => $language) {
            $tabs[] = Tab::make($language, [
                FormBuilder::make()
                    ->fields([
                        Hidden::make('id'),
                        Hidden::make('static_data_id'),
                        Hidden::make('lang'),
                        $this->getFieldByType($item)
                    ])
                    ->fill([
                        'id' => $data[$lang]->id ?? '',
                        'static_data_id' => $data[$lang]->static_data_id ?? $item->id,
                        'lang' => $lang,
                        'data' => $data[$lang]->data ?? ''
                    ])
                    ->asyncMethod('saveDataValue')
                    ->method(FormMethod::POST)
            ]);
        }

		return [
            Box::make([
                Tabs::make($tabs)
            ])
        ];
	}

    protected function getFieldByType($item)
    {
        switch ($item->type) {
            case "editor": {
                $extra = $item->extra;
                $editor = TinyMce::make(__('moonshine-static-data::main.value'),'data');
                if (isset($extra['menubar']) && is_string($extra['menubar'])) $editor->menubar($extra['menubar']);
                if (isset($extra['toolbar']) && is_string($extra['toolbar'])) $editor->toolbar($extra['toolbar']);
                return $editor;
            }
            case "interval": {
                return Json::make(__('moonshine-static-data::main.interval'), 'data')
                    ->fields([
                        Number::make(__('moonshine-static-data::main.from'), 'from')
                            ->default(5),
                        Number::make(__('moonshine-static-data::main.to'), 'to')
                            ->default(15)
                    ])
                    ->onAfterApply(function (StaticData $item, $value) {
                        StaticData::where('id', $item->id)->update(['data' => $value]);
                    })
                    ->creatable(false);
            }
            case "image": {
                $extra = $item->extra;
                $crop = Cropper::make(__('moonshine-static-data::main.image'), 'data')
                    ->ratio((float) ($extra['ratio'] ?? 0))
                    ->disk($extra['disk'] ?? 'public');
                if (isset($extra['dir'])) $crop->dir($extra['dir'] ?? '');
                if (isset($extra['mode'])) $crop->mode((int) ($extra['mode'] ?? 1));
                return $crop;
            }
            case "text": {
                return Text::make(__('moonshine-static-data::main.text'), 'data');
            }
            case "phone": {
                $phone = Phone::make(__('moonshine-static-data::main.phone'), 'data');
                if (isset($extra['mask'])) $phone->mask($extra['mask'] ?? '');
                return $phone;
            }
            default: return Text::make(__('moonshine-static-data::main.text'), 'data');
        }
    }
}
