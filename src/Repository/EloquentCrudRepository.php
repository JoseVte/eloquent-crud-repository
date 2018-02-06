<?php

namespace Eloquent\Crud\Repository;

use Eloquent\Crud\Exception\AccessDeniedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentCrudRepository implements CrudRepository
{
    /**
     * @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * EloquentCrudRepository constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * All the models. If the model has SoftDeletes, use the other methods to obtain all the models: allWithTrashed, allTrashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(array $with = [])
    {
        return $this->model->with($with)->get();
    }

    /**
     * All the models with trashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allWithTrashed(array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            return $this->model->withTrashed()->with($with)->get();
        }

        return $this->all($with);
    }

    /**
     * All the models trashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Support\Collection
     */
    public function allTrashed(array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            return $this->model->onlyTrashed()->with($with)->get();
        }

        return collect([]);
    }

    /**
     * Find a model by the primary key.
     *
     * @param mixed $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function find($id, array $with = [])
    {
        $model = $this->model->with($with)->findOrFail($id);
        $this->checkCanShow($model);

        return $model;
    }

    /**
     * Find a model with the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function findWithTrashed($id, array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            $model = $this->model->with($with)->withTrashed()->findOrFail($id);

            $this->checkCanShow($model);

            return $model;
        }

        return $this->find($id, $with);
    }

    /**
     * Find a model only in the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function findTrashed($id, array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            $model = $this->model->with($with)->onlyTrashed()->findOrFail($id);
            $this->checkCanShow($model);

            return $model;
        }

        throw (new ModelNotFoundException())->setModel(get_class($this->model));
    }

    /**
     * Find by a field and value.
     *
     * @param mixed  $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function findBy($field, $value, $comparison = '=', $strict = true, array $with = [])
    {
        $query = $this->model->with($with)->where($field, $comparison, $value);

        if ($strict) {
            $model = $query->firstOrFail();
        } else {
            $model = $query->first();
        }

        $this->checkCanShow($model);

        return $model;
    }

    /**
     * Find by a field and value with trashed models.
     *
     * @param mixed  $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function findByWithTrashed($field, $value, $comparison = '=', $strict = true, array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            $query = $this->model->with($with)->withTrashed()->where($field, $comparison, $value);

            if ($strict) {
                $model = $query->firstOrFail();
            } else {
                $model = $query->first();
            }

            $this->checkCanShow($model);

            return $model;
        }

        return $this->findBy($field, $value, $comparison, $strict, $with);
    }

    /**
     * Find by a field and value only in trashed models.
     *
     * @param mixed  $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function findByTrashed($field, $value, $comparison = '=', $strict = true, array $with = [])
    {
        if ($this->hasSoftDeletes()) {
            $query = $this->model->with($with)->onlyTrashed()->where($field, $comparison, $value);

            if ($strict) {
                $model = $query->firstOrFail();
            } else {
                $model = $query->first();
            }

            $this->checkCanShow($model);

            return $model;
        }

        if ($strict) {
            throw (new ModelNotFoundException())->setModel(get_class($this->model));
        }

        return null;
    }

    /**
     * Gets a new model with some fields filled (optional).
     *
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel(array $params = [])
    {
        return new $this->model($params);
    }

    /**
     * Formats the model to use in APIs.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function formatModel($model)
    {
        $result = $model->toArray();
        if (isset($this->model->select) && is_array($this->model->select)) {
            array_intersect_key($result, $this->model->select);
        }

        return $result;
    }

    /**
     * Creates a model.
     *
     * @param array $params The model fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function create($params)
    {
        $this->checkCanCreate($params);
        $model = $this->model->create($params);

        return $model->fresh();
    }

    /**
     * Updates a model.
     *
     * @param int   $id     The model's ID
     * @param array $params The model fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function update($id, $params)
    {
        $model = $this->model->findOrFail($id);
        $this->checkCanUpdate($model, $params);

        $model->update($params);

        return $model->fresh();
    }

    /**
     * Deletes a model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function delete($id)
    {
        $model = $this->model->findOrFail($id);
        $this->checkCanDelete($model);

        return $model->delete();
    }

    /**
     * Force-deletes a model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function forceDelete($id)
    {
        if ($this->hasSoftDeletes()) {
            $model = $this->model->withTrashed()->findOrFail($id);
        } else {
            $model = $this->model->findOrFail($id);
        }
        $this->checkCanDelete($model);

        return $model->forceDelete();
    }

    /**
     * Restores a previously deleted model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    public function restore($id)
    {
        if ($this->hasSoftDeletes()) {
            $model = $this->model->onlyTrashed()->findOrFail($id);
            $this->checkCanRestore($model);

            return $model->restore();
        }

        return false;
    }

    /**
     * Paginates a query.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query Query
     * @param int                                                                      $page  Page to show
     * @param int                                                                      $limit Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function paginate($query, $page = 0, $limit = 15)
    {
        $page = intval($page);
        $count = $query->count();
        $pages = ceil($count / $limit);

        $result = $query->skip($page * $limit)->limit($limit)->get();

        return (object) [
            'result' => $result,
            'total' => $count,
            'page' => $page,
            'pages' => $pages,
        ];
    }

    /**
     * Paginates a collection.
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $collection Collection
     * @param int                                                                     $page       Page to show
     * @param int                                                                     $limit      Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function paginateCollection($collection, $page = 0, $limit = 15)
    {
        $page = intval($page);
        $count = $collection->count();
        $pages = ceil($count / $limit);

        $result = $collection->slice($page * $limit, $limit);

        return (object) [
            'result' => $result,
            'total' => $count,
            'page' => $page,
            'pages' => $pages,
        ];
    }

    /**
     * Gets the model paginated.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function pagination($page = 0, $limit = 15)
    {
        return $this->paginate($this->model, $page, $limit);
    }

    /**
     * Gets the model paginated with trashed models.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function paginationWithTrashed($page = 0, $limit = 15)
    {
        if ($this->hasSoftDeletes()) {
            $query = $this->model->withTrashed();
        } else {
            $query = $this->model;
        }
        return $this->paginate($query, $page, $limit);
    }

    /**
     * Gets the model paginated only trashed models.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function paginationOnlyTrashed($page = 0, $limit = 15)
    {
        if ($this->hasSoftDeletes()) {
            $query = $this->model->onlyTrashed();
        } else {
            $query = $this->model->where(0, 1);
        }

        return $this->paginate($query, $page, $limit);
    }

    /**
     * Checks if the user is allowed to see an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    protected function checkCanShow($model)
    {
        if (!$this->canShow($model)) {
            throw new AccessDeniedException($model);
        }
    }

    /**
     * Checks if the user is allowed to create an instance of this model.
     *
     * @param array $params
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    protected function checkCanCreate($params)
    {
        if (!$this->canCreate($params)) {
            throw new AccessDeniedException($params);
        }
    }

    /**
     * Checks if the user is allowed to update an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $newValues New Values
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    protected function checkCanUpdate($model, $newValues)
    {
        if (!$this->canUpdate($model, $newValues)) {
            throw new AccessDeniedException($model, $newValues);
        }
    }

    /**
     * Checks if the user is allowed to delete an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    protected function checkCanDelete($model)
    {
        if (!$this->canDelete($model)) {
            throw new AccessDeniedException($model);
        }
    }

    /**
     * Checks if the user is allowed to restore an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @throws \Eloquent\Crud\Exception\AccessDeniedException
     */
    protected function checkCanRestore($model)
    {
        if (!$this->canRestore($model)) {
            throw new AccessDeniedException($model);
        }
    }

    /**
     * Determines if the user is allowed to see an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool whether the user is allowed or not
     */
    protected function canShow($model)
    {
        return true;
    }

    /**
     * Determines if the user is allowed to create an instance of this model.
     *
     * @param array $params Parameters of the new instance
     *
     * @return bool whether the user is allowed or not
     */
    protected function canCreate($params)
    {
        return true;
    }

    /**
     * Determines if the user is allowed to update an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $newValues New Values
     *
     * @return bool whether the user is allowed or not
     */
    protected function canUpdate($model, $newValues)
    {
        return true;
    }

    /**
     * Determines if the user is allowed to delete an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool whether the user is allowed or not
     */
    protected function canDelete($model)
    {
        return true;
    }

    /**
     * Determines if the user is allowed to restore an instance of this model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return bool whether the user is allowed or not
     */
    protected function canRestore($model)
    {
        return true;
    }

    /**
     * Check if the model has the soft-deletes enabled.
     *
     * @return bool
     */
    protected function hasSoftDeletes()
    {
        return array_has(class_uses($this->model), 'Illuminate\Database\Eloquent\SoftDeletes');
    }
}
