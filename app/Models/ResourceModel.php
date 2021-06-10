<?php

namespace App\Models;

use App\Models\Relations\HasManySyncable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * @mixin IdeHelperResourceModel
 */
class ResourceModel extends Model
{
    use Filterable, RevisionableTrait;

    protected static $title;
    protected static $form = [];
    protected static $breadcrumbs = ['Administracija', 'Zadano'];

    protected static $edit_in_dialog = false;
    protected static $files_directory = '/app/uploads/';

    protected static $header = [
        'title' => 'Predmeti',
        'breadcrumbs' => ['Administration', 'Subjects'],
    ];

    protected static $default_widths = ['xs' => 12, 'sm' => 12, 'md' => 6, 'lg' => 4, 'xl' => 3];

    protected static $action_buttons = [
        'table' => [
            ['icon' => 'mdi-pencil', 'callback' => 'edit-item'],
            ['icon' => 'mdi-delete', 'callback' => 'delete-item'],
        ],
        'heading' => [
            /*
            [
                'icon' => 'mdi-cloud-print-outline',
                'tooltip' => 'Ispis', 'callback' => 'print-page',
            ]
            */
        ],
    ];

    public static function getFormData($exclude = 'none')
    {
        $form = [];

        foreach (static::$form as $index => $row) {
            if (self::checkIsExcluded($row, $exclude)) continue;

            $item = [];
            $item['column'] = $row['column'] ?? $row['relation']['name'];
            $item['field'] = $row['field'] ?? $row['column'];
            $item['label'] = $row['label'] ?? $item['field'];
            $item['type'] = $row['type'] ?? (isset($row['items']) || isset($row['relation']) ? 'select' : 'text');
            $item['rules'] = $row['rules'] ? self::prepareRules($row['rules']) : '';
            $item['hasSec'] = $row['hasSec'] ?? false;

            if (isset($row['relation'])) {
                $relation = $row['relation']['name'];
                $model = self::$relation()->getModel();
                $query = $model->query();
                $model::index(request(), $query);

                $item['relation']['pivot'] = isset($row['relation']['pivot']) && $row['relation']['pivot'];
                $item['column'] = Str::snake($row['relation']['name']);
                $item['relation']['name'] = $item['column'];
                $item['relation']['expose'] = $row['relation']['expose'];
                $item['items'] = $query->select('id as value', $item['relation']['expose'] . ' as text')->get(); // TODO (custom): ovo ne valja, treba dohvatiti sve townshipe i isfiltrirati ih ako ima atribute 'filter'
                $item['multiple'] = isset($row['multiple']) && $row['multiple'];

                if ($item['relation']['pivot']) {
                    $item['column'] = $row['relation']['name'];
                    $item['field'] = $item['column'];
                }
            } else if ($item['type'] == 'select') {
                $item['items'] = $row['items'];
            }

            if (isset($row['icon'])) $item['icon'] = $row['icon'];
            if (isset($row['width'])) $item['width'] = $row['width'];
            if (isset($row['height'])) $item['height'] = $row['height'];

            array_push($form, $item);
        }

        return $form;
    }

    public function getTableHeaders()
    {
        $tableHeaders = [];
        $formData = $this->getFormData('index');

        foreach ($formData as $item) {
            array_push($tableHeaders, [
                'text' => $item['label'],
                'value' => $item['column'],
                'searchable' => $item['type'] != 'select'
                    && $item['type'] != 'date'
            ]);
        }

        array_push($tableHeaders, [
            'text' => 'Akcije',
            'value' => 'actions',
            'sortable' => false,
        ]);

        return $tableHeaders;
    }

    protected static function checkIsExcluded($item, $excluded)
    {
        return isset($item['excluded']) && strpos($item['excluded'], $excluded) !== false;
    }

    protected static function prepareRules($rules)
    {
        $prepared = [];

        $rules = explode('|', $rules);
        $interferences = ['nullable', 'exists', 'unique', 'array', 'date'];

        foreach ($rules as $rule) {
            foreach ($interferences as $interference) {
                if (str_contains($rule, $interference)) continue 2;
            }

            $prepared[] = $rule;
        }

        return implode('|', $prepared);
    }

    public static function getData()
    {
        return static::$form;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            static::checkPolicy('create', $model);
        });

        static::updating(function ($model) {
            static::checkPolicy('update', $model);
        });

        static::deleting(function ($model) {
            static::checkPolicy('delete', $model);
        });
    }

    public static function index(Request $request, &$query)
    {
    }

    public static function manageResource(Request $request, $id = null)
    {
        $validated = static::validateModel($request->all());
        if ($id) {
            $model = static::findOrFail($id);
            $model->update($validated);
        } else {
            $model = static::create($validated);
        }

        if ($request->relations) {
            $relations = (array)$request->relations;
            foreach ($relations as $relation => $options) {
                if ($options['method'] && $options['data'])
                    $model->manageRelation($relation, $options['method'], (array)$options['data']);
            }
        }

//        $files = array_column(static::$form, 'type'); // TODO (custom): ne valjda++a
//        foreach ($files as $attribute) $model->$attribute = $request->$attribute; // TODO (custom): Physically delete old resource's file(s)

        return $model;
    }

    public function manageRelation(string $relation, string $method, array $data)
    {
        $relatedModel = $this->$relation()->getModel();
        if (method_exists($this->$relation(), 'getPivotClass')) {
            $relatedModel = $this->$relation()->getPivotClass();
        }

        if ($method != 'detach') {
            foreach ($data as $item) {
                if (!is_int($item)) {
                    $relatedModel::validateModel($item);
                }
            }
        }

        switch ($method) {
            case 'associate':
                $relData = $relatedModel::create($data[0])->id;
                break;
            case 'create':
                $relData = $data[0];
                break;
            default:
                $relData = $data;
        }

        $this->$relation()->$method($relData);
        return $this->load($relation);
    }

    public static function validateModel($data)
    {
        return Validator::make((array)$data, static::getValidation())->validate();
    }

    public static function getValidation()
    {
        $validation = [];
        foreach (static::$form as $item) {
            $validation[$item['column'] ?? $item['field']] = $item['rules'];
        }
        return $validation;
    }

    public static function getHeaders()
    {
        return array_column(static::$form, 'column');
    }

    public function getFormDataOld()
    {
        $fields = static::getFromDataByKey('form');
        $validation = static::getFromDataByKey('validation');

        foreach ($fields as $key => &$field) {
            if (array_key_exists($key, $validation)) {
                $field['validation'] = $validation[$key];
            }
        }

        return $fields;
    }

    public static function getFromDataByKey($key, $keysOnly = false)
    {
        $items = [];
        foreach (static::$data as $item => $value) {
            if (!array_key_exists($key, (array)$value))
                continue;
            if ($value[$key])
                $items[$item] = $value[$key];
        }

        if ($keysOnly)
            return array_keys($items);

        return $items;
    }

    public static function checkPolicy($policyPermission, $model = null)
    {
        /*
        if (Auth::user()->cannot($policyPermission, $model))
            abort(403, 'You don\'t have permission to access this resource or action!'
                . 'Required permission is ' . $policyPermission . ' ' . static::class);
        */
        return true;
    }

    public function getModelData(Request $request)
    {
        $breadcrumbs = [];

        foreach (static::$breadcrumbs as $breadcrumb)
            array_push($breadcrumbs, ['text' => $breadcrumb]);

        array_push($breadcrumbs, ['text' => static::$title]);

        return [
            'title' => static::$title,
            'breadcrumbs' => $breadcrumbs,
            'edit_in_dialog' => static::$edit_in_dialog,
            'action_buttons' => static::$action_buttons,
        ];
    }

    public function getActionButtons($action = 'all')
    {
        if ($action == 'all') {
            return static::$action_buttons;
        }

        return static::$action_buttons[$action];
    }

    public function getTitle()
    {
        return static::$title;
    }

    public function getBreadcrumbs()
    {
        $breadcrumbs = static::$breadcrumbs;

        array_push($breadcrumbs, static::getTitle());

        return $breadcrumbs;
    }

    public function getResourceRelations()
    {
        return array_column(static::$form, 'relation');
    }

    public function getDefaultFilters()
    {
        $form = static::getFormData();
        $filters = [];

        foreach ($form as $item) {
            if (isset($item['items'])) {
                $new_item = [];
                $new_item['label'] = $item['label'];
                $new_item['field'] = $item['field'];
                $new_item['items'] = $item['items'];

                $filters[] = $new_item;
            }
        }

        return $filters;
    }

    public function getDefaultWidth($screen_size = 'all')
    {
        if ($screen_size == 'all') {
            return static::$default_widths;
        }

        return static::$default_widths[$screen_size];
    }

    /**
     * HasMany relationship with additional custom method "sync"
     *
     * @return HasManySyncable
     */
    public function hasManySyncable($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManySyncable(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    public function uploadFile(Request $request, $fileAttribute)
    {
        // self::checkPermission('upload files', true);

        if ($file = $request->file($fileAttribute)) {
            return $this->manageFile($file);
        }

        return null;
    }

    public function manageFile($file, $i = 0)
    {
        $filename = time() . $i . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path() . static::$files_directory, $filename);

        return $filename;
    }
}
