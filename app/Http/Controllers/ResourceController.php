<?php

namespace App\Http\Controllers;

use App\Models\ResourceModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    protected static $modelName;
    protected static $modelNamespace = 'App\\Models\\';

    /**
     * @var ResourceModel
     */
    protected $model;

    public function __construct()
    {
        $this->model = self::getModelByName(static::$modelName);
    }

    public static function getModelByName($name)
    {
        return static::$modelNamespace . $name;
    }

    public function index(Request $request)
    {
        $query = $this->model::query();

//        $this->model::checkPolicy('viewAny', $this->model);
        $this->model::index($request, $query);
        return $this->indexReturn($request, $query);
    }

    public function indexRelation(Request $request, $id, $relation)
    {
        $item = $this->model::findOrFail($id);
        $this->model::checkPolicy('view', $item);

        $query = $item->$relation();

        $item->$relation()->getModel()::index($request, $query);

        return $this->indexReturn($request, $query);
    }

    public function indexReturn(Request $request, $query)
    {
//        if ($this->hasTrait(Filterable::class)) {
//            $query->filter($request->filter ?? [], $request->filterRelation ?? []);
//        }

        if ($request->has('filter') || $request->has('filterRelation')) {
            $query->filter($request->filter ?? [], $request->filterRelation ?? []);
        }

        $this->sortResource($request, $query);
        $this->withRelations($request, $query);

        if ($request->limit)
            $query->limit($request->limit);

        $rowsPerPage = $request->rowsPerPage;
        if ($rowsPerPage < 0)
            $rowsPerPage = $query->count();

        $data = $query->paginate($rowsPerPage);
        return $data;
    }

    public function store(Request $request)
    {
        return $this->model::manageResource($request);
    }

    public function update(Request $request, $id)
    {
        return $this->model::manageResource($request, $id);
    }

    public function show(Request $request, $id)
    {
        $query = $this->model::query();
        $this->withRelations($request, $query);

        $item = $query->findOrFail($id);
        $this->model::checkPolicy('view', $item);

        return $item;
    }

    public function destroy(Request $request, $id)
    {
        // Forbid deleting for now
        //abort(403);

        $item = $this->model::findOrFail($id);
        $item->delete();

        return response('Success', 204);
    }

    public function manageRelation(Request $request, $id, string $relation)
    {
        $request->validate([
            'method' =>
                'required|string|in:attach,detach,sync,toggle,syncWithoutDetaching,create,createMany,associate,dissociate',
            'data' => 'present'
        ]);

        $method = $request->get('method');

        $item = $this->model::findOrFail($id);

        $data = (array)$request->get('data');

        return $item->manageRelation($relation, $method, $data);
    }

    public function withRelations(Request $request, &$query)
    {
        $requestWith = (array)$request->with;
//        $query->with($requestWith);

        foreach ($requestWith as $relation) {
            $relatedModel = $query->getModel();
            //fix ako se radi o 'dot' notaciji za duboke relacije. Npr. with('user.parentOne')
            $explodedRelation = explode('.', $relation);
            $relationshipModel = $query->getModel();
            foreach ($explodedRelation as $relKey => $relValue) {
                // Stripaj select constraintove
                $relValue = Str::before($relValue, ':');

//            // Ako se radi o belongsToMany treba ovo da dohvati ispravan model
                if (method_exists($relatedModel, $relValue) && method_exists($relatedModel->$relValue(), 'getPivotClass')) {
                    $pivotClass = $relatedModel->$relValue()->getPivotClass();
                    $relationshipModel = new $pivotClass;
                } else
                    $relationshipModel = $relationshipModel->$relValue()->getModel();
            }

            // Ne more više selectat nakon ovog updatea, smislit kako to izvest
            $relationWithoutSelectConstraints = Str::before($relation, ':');
            $query->with([$relationWithoutSelectConstraints => function ($query2) use ($request, $relationshipModel) {
                $relationshipModel::index($request, $query2);
            }]);
        }

        $requestWithCount = (array)$request->withCount;
        $query->withCount($requestWithCount);
    }

    protected function sortResource(Request $request, &$query)
    {
        if (!$request->sortBy)
            return;

        $sorts = (array)$request->sortBy;
        $sortDesc = (array)$request->sortDesc;

        foreach ($sorts as $i => $sort) {
            $desc = filter_var($sortDesc[$i], FILTER_VALIDATE_BOOLEAN);
            $query->orderBy($sort, $desc ? 'desc' : 'asc');
        }
    }

    public function getModel($model)
    {
        return 'App\\Models\\' . $model;
    }

    public function getFrontendData()
    {
        /** @var ResourceModel $model */
        $model = new $this->model;

        return [
            'heading' => [
                'title' => $model->getTitle(),
                'breadcrumbs' => $model->getBreadcrumbs(),
                'buttons' => $model->getActionButtons('heading'),
                'filters' => $model->getDefaultFilters(),
            ],
            'table' => [
                'items' => $model->getTableHeaders(),
                'buttons' => $model->getActionButtons('table')
            ],
            'form' => [
                'items' => $model->getFormData('add'),
                'width' => $model->getDefaultWidth(),
            ],
            'relations' => $model->getResourceRelations(),
        ];
    }

    public function getFormData()
    {
        $model = new $this->model;

        return $model->getFormData('add');
    }

    public function getTableHeaders()
    {
        $model = new $this->model;

        return $model->getTableHeaders();
    }

    public function hasTrait($trait)
    {
        return in_array($trait, class_uses($this->model));
    }
}
