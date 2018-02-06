<?php

namespace Laravel\Crud\Repository;

interface CrudRepository
{
    /**
     * All the models. If the model uses SoftDeletes, use the other methods to obtain all the models: allWithTrashed, allTrashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(array $with = []);

    /**
     * All the models with trashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allWithTrashed(array $with = []);

    /**
     * All the models trashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Support\Collection
     */
    public function allTrashed(array $with = []);

    /**
     * Find a model by the primary key.
     *
     * @param mixed $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function find($id, array $with = []);

    /**
     * Find a model with the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function findWithTrashed($id, array $with = []);

    /**
     * Find a model only in the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function findTrashed($id, array $with = []);

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
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function findBy($field, $value, $comparison = '=', $strict = true, array $with = []);

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
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function findByWithTrashed($field, $value, $comparison = '=', $strict = true, array $with = []);

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
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function findByTrashed($field, $value, $comparison = '=', $strict = true, array $with = []);

    /**
     * Gets a new model with some fields filled (optional).
     *
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel(array $params = []);

    /**
     * Formats the model to use in APIs.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function formatModel($model);

    /**
     * Creates a model.
     *
     * @param array $params The model fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function create($params);

    /**
     * Updates a model.
     *
     * @param int   $id     The model's ID
     * @param array $params The model fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function update($id, $params);

    /**
     * Deletes a model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function delete($id);

    /**
     * Force-deletes a model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function forceDelete($id);

    /**
     * Restores a previously deleted model.
     *
     * @param int $id The model's ID
     *
     * @return bool
     *
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function restore($id);

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
    public function paginate($query, $page = 0, $limit = 15);

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
    public function paginateCollection($collection, $page = 0, $limit = 15);

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
    public function pagination($page = 0, $limit = 15);

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
    public function paginationWithTrashed($page = 0, $limit = 15);

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
    public function paginationOnlyTrashed($page = 0, $limit = 15);
}
