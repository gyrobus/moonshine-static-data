<?php

declare(strict_types=1);

use Gyrobus\StaticData\Models\StaticData;
use Gyrobus\StaticData\Models\StaticDataValue;
use Gyrobus\StaticData\Pages\GroupIndex;
use Gyrobus\StaticData\Pages\GroupItemForm;
use Gyrobus\StaticData\Pages\GroupItems;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\ToastType;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use Gyrobus\MoonshineCropper\Fields\Cropper;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<StaticData>
 */
class StaticDataResource extends ModelResource
{
    protected string $model = StaticData::class;

    protected string $column = 'name';

    protected array $with = ['data'];

    public function getTitle(): string
    {
        return __('moonshine-static-data::main.title');
    }

    public function pages(): array
    {
        return [
            GroupIndex::class,
            GroupItems::class,
            GroupItemForm::class,

            IndexPage::class,
        ];
    }

    /**
     * @return list<FieldContract>
     */
    public function indexFields(): array
    {
        return [
            ID::make('ID', 'id'),
            Text::make(__('moonshine-static-data::main.param'), 'name'),
            Text::make(__('moonshine-static-data::main.group'), 'group')
        ];
    }

    /**
     * @return FieldContract
     */
    public function formFields(): array
    {
        $item = $this->getItem();

        $form = [
            ID::make()->sortable()
        ];

        if ($item) {

            $form[] = Text::make(__('moonshine-static-data::main.name'), 'name')->readonly();

            switch ($item->type) {
                case "editor": {
                    $form[] = TinyMce::make(__('moonshine-static-data::main.value'),'data');
                }
                    break;
                case "interval": {
                    $form[] = Json::make(__('moonshine-static-data::main.interval'), 'data')
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
                    break;
                case "image": {
                    $extra = $item->extra;
                    $crop = Cropper::make(__('moonshine-static-data::main.image'), 'data')
                        ->ratio((float) ($extra['ratio'] ?? 0))
                        ->disk($extra['disk'] ?? 'public');
                    if (isset($extra['dir'])) $crop->dir($extra['dir'] ?? '');
                    if (isset($extra['mode'])) $crop->mode((int) ($extra['mode'] ?? 1));
                    $form[] = $crop;
                }
                    break;
                case "phone": {
                    $phone = Phone::make(__('moonshine-static-data::main.phone'), 'data');
                    if (isset($extra['mask'])) $phone->mask($extra['mask'] ?? '');
                    $form[] = $phone;
                }
                break;
                case "text": {
                    $form[] = Text::make(__('moonshine-static-data::main.text'), 'data');
                }
                break;
            }
        } else {
            $form[] = Text::make(__('moonshine-static-data::main.name'), 'name');
            $form[] = Slug::make('Slug', 'slug');
            $form[] = Select::make(__('moonshine-static-data::main.type'), 'type')->options([
                'editor' => __('moonshine-static-data::main.editor'),
                'interval' => __('moonshine-static-data::main.interval'),
                'image' => __('moonshine-static-data::main.image'),
                'text' => __('moonshine-static-data::main.text'),
            ]);
            $form[] = Select::make(__('moonshine-static-data::main.group'), 'group')
                ->options(function () {
                    return StaticData::select('group')->groupBy('group')->get()->keyBy('group')->map(fn ($item) => $item->group)->toArray();
                });
        }

        return [
            Box::make($form)
        ];
    }

    public function filters(): array
    {
        return [
            Select::make(__('moonshine-static-data::main.group'), 'group')
                ->options(function () {
                    return StaticData::select('group')->groupBy('group')->get()->keyBy('group')->map(fn ($item) => $item->group)->toArray();
                })
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
        ];
    }

    /**
     * @param StaticData $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }

    public function saveDataValue(MoonShineRequest $request): MoonShineJsonResponse
    {
        if ($request->input('id')) {
            $data = StaticDataValue::find($request->input('id'));
            if ($data && $data->update(['data' => $request->input('data')])) {
                return MoonShineJsonResponse::make()->toast(__('moonshine-static-data::main.save'), ToastType::SUCCESS);
            }
        } else {
            if ($request->input('static_data_id')) {

                StaticDataValue::updateOrCreate([
                    'static_data_id' => $request->input('static_data_id'),
                    'lang' => $request->input('lang')
                ],[
                    'data' => $request->input('data'),
                ]);

                return MoonShineJsonResponse::make()->toast(__('moonshine-static-data::main.saveSuccess'), ToastType::SUCCESS);
            }
        }

        return MoonShineJsonResponse::make()->toast(__('moonshine-static-data::main.saveError'), ToastType::ERROR);
    }
}
