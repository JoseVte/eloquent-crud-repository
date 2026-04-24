<?php

namespace Eloquent\Crud\Repository;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent\Crud\Exception\AccessDeniedException;

interface CrudRepository
{
    /**
     * Return the model to allow creating custom queries.
     *
     * @return Model|SoftDeletes
     */
    public function model(): Model;

    /**
     * All the models. If the model uses SoftDeletes, use the other methods to get all the models: allWithTrashed, allTrashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(array $with = []) : Collection;

    /**
     * All the models with trashed.
     *
     * @param array $with
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allWithTrashed(array $with = []) : Collection;

    /**
     * All the models trashed.
     *
     * @param array $with
     *
     * @return Collection
     */
    public function allTrashed(array $with = []): Collection;

    /**
     * Find a model by the primary key.
     *
     * @param mixed $id
     * @param array $with
     *
     * @throws AccessDeniedException
     *
     * @return Model
     */
    public function find(int $id, array $with = []) : Model;

    /**
     * Find a model with the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @throws AccessDeniedException
     *
     * @return Model
     */
    public function findWithTrashed(int $id, array $with = []) : Model;

    /**
     * Find a model only in the trashed models.
     *
     * @param int   $id
     * @param array $with
     *
     * @throws AccessDeniedException
     *
     * @return Model
     */
    public function findTrashed(int $id, array $with = []) : Model;

    /**
     * Find by a field and value.
     *
     * @param string $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @throws AccessDeniedException
     *
     * @return Model|null|static
     */
    public function findBy(string $field, $value, string $comparison = '=', bool $strict = true, array $with = []) : ?Model;

    /**
     * Find by a field and value with trashed models.
     *
     * @param string $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @throws AccessDeniedException
     *
     * @return Model|null|static
     */
    public function findByWithTrashed(string $field, $value, string $comparison = '=', bool $strict = true, array $with = []) : ?Model;

    /**
     * Find by a field and value only in trashed models.
     *
     * @param string $field
     * @param mixed  $value
     * @param string $comparison
     * @param bool   $strict
     * @param array  $with
     *
     * @throws AccessDeniedException
     *
     * @return Model|null|static
     */
    public function findByTrashed(string $field, $value, string $comparison = '=', bool $strict = true, array $with = []) : ?Model;

    /**
     * Gets a new model with some fields filled (optional).
     *
     * @param array $params
     *
     * @return Model
     */
    public function newModel(array $params = []): Model;

    /**
     * Formats the model to use in APIs.
     *
     * @param Model $model
     *
     * @return array
     */
    public function formatModel(Model $model): array;

    /**
     * Creates a model.
     *
     * @param array $params The model fields
     *
     * @throws AccessDeniedException
     *
     * @return Model
     */
    public function create(array $params): Model;

    /**
     * Updates a model.
     *
     * @param int   $id     The model's ID
     * @param array $params The model fields
     *
     * @throws AccessDeniedException
     *
     * @return Model
     */
    public function update(int $id, array $params): Model;

    /**
     * Deletes a model.
     *
     * @param int $id The model's ID
     *
     * @throws AccessDeniedException
     * @throws Exception
     *
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Force-deletes a model.
     *
     * @param int $id The model's ID
     *
     * @throws AccessDeniedException
     * @throws Exception
     *
     * @return bool
     */
    public function forceDelete(int $id): bool;

    /**
     * Restores a previously deleted model.
     *
     * @param int $id The model's ID
     *
     * @throws AccessDeniedException
     *
     * @return bool
     */
    public function restore(int $id): bool;

    /**
     * Paginates a query.
     *
     * @param \Illuminate\Database\Query\Builder|Builder $query Query
     * @param int                                        $page  Page to show
     * @param int                                        $limit Items per page
     *
     * @return object Json with the result
     *                - result: Array with the result
     *                - total: Total of items
     *                - page:   Current page
     *                - pages: Total of pages
     */
    public function paginate($query, int $page = 0, int $limit = 15) : object;

    /**
     * Paginates a collection.
     *
     * @param \Illuminate\Database\Eloquent\Collection|Collection $collection Collection
     * @param int                                                 $page       Page to show
     * @param int                                                 $limit      Items per page
     *
     * @return object{result: Collection, total: int, page: int, pages: int}
     *                                                                       Json with the result
     *                                                                       - result: Array with the result
     *                                                                       - total: Total of items
     *                                                                       - page:   Current page
     *                                                                       - pages: Total of pages
     */
    public function paginateCollection(Collection $collection, int $page = 0, int $limit = 15) : object;

    /**
     * Gets the model paginated.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object{result: Collection, total: int, page: int, pages: int}
     *                                                                       Json with the result
     *                                                                       - result: Array with the result
     *                                                                       - total: Total of items
     *                                                                       - page:   Current page
     *                                                                       - pages: Total of pages
     */
    public function pagination(int $page = 0, int $limit = 15): object;

    /**
     * Gets the model paginated with trashed models.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object{result: Collection, total: int, page: int, pages: int}
     *                                                                       Json with the result
     *                                                                       - result: Array with the result
     *                                                                       - total: Total of items
     *                                                                       - page:   Current page
     *                                                                       - pages: Total of pages
     */
    public function paginationWithTrashed(int $page = 0, int $limit = 15) : object;

    /**
     * Gets the model paginated only trashed models.
     *
     * @param int $page  Page to show
     * @param int $limit Items per page
     *
     * @return object{result: Collection, total: int, page: int, pages: int}
     *                                                                       Json with the result
     *                                                                       - result: Array with the result
     *                                                                       - total: Total of items
     *                                                                       - page:   Current page
     *                                                                       - pages: Total of pages
     */
    public function paginationOnlyTrashed(int $page = 0, int $limit = 15) : object;
}
