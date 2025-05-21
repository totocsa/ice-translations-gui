<?php

namespace Totocsa\TranslationsGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Totocsa\Icseusd\Http\Controllers\IcseusdController;
use Totocsa\DatabaseTranslationLocally\Models\Locale;
use Totocsa\DatabaseTranslationLocally\Models\Translationoriginal;
use Totocsa\DatabaseTranslationLocally\Models\Translationvariant;
use Totocsa\DatabaseTranslationLocally\Validation\Facades\Validator;

class TranslationsController extends IcseusdController
{
    public $vuePageDir = 'vendor/totocsa/ice-translations-gui/resources/js/Pages';
    public $modelClassName = Translationvariant::class;

    public $sort = [
        'field' => 'translationoriginal-category',
        'direction' => 'asc',
    ];

    public $orders = [
        'index' => [
            'filters' => ['translationoriginal-category', 'translationoriginal-subtitle', 'translationvariants-locales_id', 'translationvariants-subtitle'],
            'sorts' => [
                'translationoriginal-category' => ['translationoriginal-category', 'translationoriginal-subtitle', 'locale-configname', 'translationvariants-subtitle'],
                'translationoriginal-subtitle' => ['translationoriginal-subtitle', 'translationoriginal-category', 'locale-configname', 'translationvariants-subtitle'],
                'translationvariants-locales_id' => ['locale-configname', 'translationoriginal-category', 'translationoriginal-subtitle', 'translationvariants-subtitle'],
                'translationvariants-subtitle' => ['translationvariants-subtitle', 'translationoriginal-category', 'translationoriginal-subtitle', 'locale-configname'],
            ],
            'item' => [
                'fields' => ['translationoriginal-category', 'translationoriginal-subtitle', 'locale-configname', 'translationvariants-subtitle'],
            ],
            'itemButtons' => ['show', 'edit', 'destroy'],
        ],
        'form' => [
            'item' => [
                'fields' => ['translationvariants-translationoriginals_id', 'translationvariants-locales_id', 'translationvariants-subtitle'],
            ],
        ],
        'show' => [
            'item' => [
                'fields' => ['translationvariants-translationoriginals_id', 'translationvariants-locales_id', 'translationvariants-subtitle'],
            ],
        ],
    ];

    public $filters = [
        'translationoriginal-category' => '',
        'translationoriginal-subtitle' => '',
        'translationvariants-locales_id' => '',
        'translationvariants-subtitle' => '',
    ];

    public function conditions()
    {
        return [
            'translationoriginal-category' => [
                'operator' => $this->ilikeORLike,
                'value' => "%{{translationoriginal-category}}%",
                'boolean' => 'and',
            ],
            'translationoriginal-subtitle' => [
                'operator' => $this->ilikeORLike,
                'value' => "%{{translationoriginal-subtitle}}%",
                'boolean' => 'and',
            ],
            'translationvariants-locales_id' => [
                'operator' => '=',
                'value' => '{{translationvariants-locales_id}}',
                'boolean' => 'and',
            ],
            'translationvariants-subtitle' => [
                'operator' => $this->ilikeORLike,
                'value' => "%{{translationvariants-subtitle}}%",
                'boolean' => 'and',
            ],
        ];
    }

    public function fields()
    {
        return [
            'filter' => [
                'translationoriginal-category' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'translationoriginal-subtitle' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'translationvariants-locales_id' => [
                    'tagName' => 'select',
                    'options' => array_merge([['value' => '', 'text' => '']], $this->getLocales_idValueTexts()),
                ],
                'translationvariants-subtitle' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'item' => [
                'translationvariants-subtitle' => [
                    'tagName' => 'EDITABLE_TEXT_STARTER',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'form' => [
                'translationvariants-locales_id' => [
                    'tagName' => 'select',
                    'options' => array_merge([['value' => '', 'text' => '']], $this->getLocales_idValueTexts()),
                ],
                'translationvariants-subtitle' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'show' => [],
        ];
    }

    public function _fields()
    {
        return [
            'translationoriginal-category' => [
                'filter' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'translationoriginal-subtitle' => [
                'filter' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'translationvariants-locales_id' => [
                'filter' => [
                    'tagName' => 'select',
                    'options' => ['additionalData', 'locales_idValueTexts'],
                    'attributes' => [],
                ],
                'form' => [
                    'tagName' => 'select',
                    'options' => ['additionalData', 'locales_idValueTexts'],
                    'attributes' => [],
                ],
            ],
            'translationvariants-subtitle' => [
                /*'editableOnIndex' => [
                    'route' => 'translations.saveTranslationSubtitle',
                    'fields' => ['key' => 'id', 'field' => 'subtitle'],
                ],*/
                'editableOnIndex' => true,
                'form' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ];
    }

    public function indexQuery(): LengthAwarePaginator
    {
        $t0 = 'translationvariants';
        $t1 = 'translationoriginal';
        $t2 = 'locale';

        $query = $this->modelClassName::query()
            ->select([
                "$t0.id",
                "$t1.category as $t1-category",
                "$t1.subtitle as $t1-subtitle",
                "$t2.configname as $t2-configname",
                "$t0.subtitle as $t0-subtitle"
            ])
            ->leftJoin("translationoriginals as $t1", "$t0.translationoriginals_id", '=', "$t1.id")
            ->leftJoin("locales as $t2", "$t0.locales_id", '=', "$t2.id");

        foreach ($this->conditions() as $k => $v) {
            if ($this->filters[$k] > 0) {
                $cond = $this->conditions()[$k];
                $value = strtr($cond['value'], $this->replaceFieldToValue());
                $query->where(str_replace('-', '.', $k), $cond['operator'], $value, $cond['boolean']);
            }
        }

        foreach ($this->orders['index']['sorts'][$this->sort['field']] as $v) {
            $query->orderBy($v, $this->sort['direction']);
        }

        $results = $query->paginate($this->paging['per_page'], ['*'], null, $this->paging['page']);

        return $results;
    }

    public function create()
    {
        $model = new Translationoriginal();

        foreach ($model->getFillable() as $v) {
            $model->{$v} = '';
        }

        $params = array_merge($this->commonParams(), compact('model'));
        return Inertia::render($this->vueComponents['create'], $params);
    }

    public function store(Request $request)
    {
        $model = new Translationoriginal();
        $rules = $model::rules($request);
        $request->validate($rules);

        $attributes = [];
        foreach ($model->getFillable() as $v) {
            $attributes[$v] = $request->get($v);
        }

        $model->setRawAttributes($attributes);
        $model->save();

        $params = array_merge($this->commonParams(), compact('model'));
        return Inertia::render($this->vueComponents['create'], $params);
    }

    public function loaderrefresh(Request $request)
    {
        //$result = app('translation.loader')->refresh('validation');
        $result = app('translation.loader')->refreshAll();

        return redirect()->route($request->route['current'], $request->route['params'] ?? []);
    }

    public function additionalIndexData()
    {
        $empty = [['value' => '', 'text' => '']];
        $locales_idValueTexts = array_merge($empty, $this->getLocales_idValueTexts());
        $translationoriginals_idValueTexts = array_merge($empty, $this->getTranslationoriginals_idValueTexts());

        return [
            'locales_idValueTexts' => $locales_idValueTexts,
            'translationoriginals_idValueTexts' => $translationoriginals_idValueTexts,
        ];
    }

    public function additionalCreateData()
    {
        $empty = [['value' => '', 'text' => '']];
        $locales_idValueTexts = array_merge($empty, $this->getLocales_idValueTexts());
        $translationoriginals_idValueTexts = array_merge($empty, $this->getTranslationoriginals_idValueTexts());

        return [
            'locales_idValueTexts' => $locales_idValueTexts,
            'translationoriginals_idValueTexts' => $translationoriginals_idValueTexts,
        ];
    }

    public function additionalEditData()
    {
        $empty = [['value' => '', 'text' => '']];
        $locales_idValueTexts = array_merge($empty, $this->getLocales_idValueTexts());
        $translationoriginals_idValueTexts = array_merge($empty, $this->getTranslationoriginals_idValueTexts());

        return [
            'locales_idValueTexts' => $locales_idValueTexts,
            'translationoriginals_idValueTexts' => $translationoriginals_idValueTexts,
        ];
    }

    public function getLocales_idValueTexts()
    {
        $models = Locale::query()->select(['id AS value', 'configname AS text'])->where('enabled', true)->orderBy('configname', 'asc')->get()->toArray();

        return $models;
    }

    public function getTranslationoriginals_idValueTexts()
    {
        $models = Translationoriginal::query()->select(['category AS value', 'category AS text'])
            ->groupBy('category')->orderBy('category', 'asc')->get()->toArray();

        return $models;
    }

    public function save(Request $request)
    {
        $data = $request->all();

        $this->saveDataNormalization($data);

        $original = $this->getOriginalData($data['original'], $request);

        if ($original['errors'] === []) {
            $translations = $this->getTranslationsData($data['translations']);

            if ($translations['allValid'] === true) {
                DB::beginTransaction();

                if (!$original['model']->exists) {
                    $original['model']->save();
                }

                $tablaName = (new Translationvariant())->getTable();
                foreach ($translations['items'] as $v) {
                    DB::table($tablaName)
                        ->where('locales_id', $v['locales_id'])
                        ->where('translationoriginals_id', $original['model']->id)
                        ->update(['subtitle' => $v['subtitle']]);

                    unset($translations['items']['locales_id']);
                }

                DB::commit();

                $translationResults = [
                    'original' => Arr::only($original['model']->attributesToArray(), ['category', 'subtitle']),
                    'translations' => $translations['items'],
                ];
            } else {
                $translationResults =                    [
                    'errors' => [
                        'translations' => $translations['errors'],
                    ],
                ];
            }
        } else {
            $translationResults = [
                'errors' => [
                    'original' => $original['errors']->toArray(),
                ]
            ];
        }

        return Inertia::render($data['component'], compact('translationResults'));
    }

    public function saveDataNormalization(&$data)
    {
        $data['original'] = isset($data['original']) && is_array($data['original'] ?? []) ? $data['original'] : [];
        $data['translations'] =  isset($data['translations']) && is_array($data['translations'] ?? []) ? $data['translations'] : [];

        $supporteds = config('laravellocalization.supportedLocales');

        foreach ($supporteds as $k => $v) {
            $data['translations'][$k] = isset($data['translations'][$k]) && is_array($data['translations'][$k] ?? []) ? $data['translations'][$k] : [];
        }
    }

    public function getOriginalData($data, $request)
    {
        $rules = Translationoriginal::rules($request);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'model' => null,
                'errors' => $validator->errors(),
            ];
        }

        $model = Translationoriginal::where('category', $data['category'])
            ->where('subtitle', $data['subtitle'])
            ->first();

        if (is_null($model)) {
            $model = new Translationoriginal();
            $model->setRawAttributes($data);
        }

        return [
            'model' => $model,
            'errors' => [],
        ];
    }

    public function getTranslationsData($data)
    {
        $allValid = true;
        $items = [];
        $errors = [];

        foreach ($data as $v) {
            $items[$v['locales_configname']] = [];

            $locales_configname = $v['locales_configname'] ?? null;
            $locales = Locale::where('configname', $locales_configname)->first();

            $items[$v['locales_configname']]['locales_id'] = is_object($locales) ? $locales->id : null;
            $items[$v['locales_configname']]['subtitle'] = $v['subtitle'] ?? '';


            $validator = Validator::make(
                $items[$v['locales_configname']],
                Arr::only(Translationvariant::rules(), ['locales_id', 'subtitle']) // Csak a subtitle szabályait vesszük ki
            );

            $errors[$v['locales_configname']] = $validator->errors()->toArray();
            $allValid = $allValid && $validator->passes();
        }

        return [
            'allValid' => $allValid,
            'items' => $items,
            'errors' => $errors,
        ];
    }

    public function import(Request $request)
    {
        $rows = $request->rows;

        $tOriginal = (new Translationoriginal())->getTable();
        $tVariant = (new Translationvariant())->getTable();
        $tLocale = (new Locale())->getTable();

        DB::beginTransaction();

        try {
            $isErrors = false;

            foreach ($rows as $rowNumber => $data) {
                // A null legyen ''
                $data['translation'] = (string) $data['translation'];

                //Hogy az esetleges validációkor megfelelő legyen az adat
                $mOriginal = null;
                $mLocale = null;

                $mOriginal = DB::table($tOriginal)
                    ->where('category', $data['category'])
                    ->where('subtitle', $data['original'])
                    ->first();

                if (is_null($mOriginal)) {
                    $mOriginal = new Translationoriginal([
                        'category' => $data['category'],
                        'subtitle' => $data['original'],
                    ]);

                    $mOriginal->save();
                }

                $mLocale = DB::table($tLocale)->where('configname', $data['language'])->first();

                DB::table($tVariant)
                    ->where('translationoriginals_id', $mOriginal->id)
                    ->where('locales_id', $mLocale->id)
                    ->update(['subtitle' => $data['translation']]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            $isErrors = true;
            DB::rollBack();
        }

        if ($isErrors) {
            $errors[$rowNumber] = $this->importValidateRow($data, $mOriginal, $mLocale);

            if (count($errors[$rowNumber]) === 0) {
                $errors[$rowNumber] = [
                    '.fullrow' => [
                        0 => [
                            'key' => 'messages',
                            'original' => 'An error occurred while saving the data.',
                            'message' => 'An error occurred while saving the data.',
                            'params' => [],
                        ],
                    ],
                ];
            }
        } else {
            $errors = [];
        }

        return response()->json(compact('errors'));
    }

    public function importValidateRow($data)
    {
        $requestOriginal = new Request([], [], [
            'category' => $data['category'],
            'subtitle' => $data['original'],
        ]);

        $rulesOriginal = Translationoriginal::rules($requestOriginal);
        $rules = [
            'category' => $rulesOriginal['category'],
            'original' => $rulesOriginal['subtitle'],
        ];

        $rulesVariant = Translationvariant::rules();
        $rules = [
            'language' => 'exists:' . Locale::class . ',configname',
            'translation' => $rulesVariant['subtitle'],
        ];

        $validator = Validator::make($data, $rules);
        $e = $validator->messages('translatable');
        return $validator->messages('translatable');
    }
}
