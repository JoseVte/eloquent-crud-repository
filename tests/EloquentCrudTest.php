<?php

use Laravel\Crud\Repository\EloquentCrudRepository;

class EloquentCrudTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \TestModel|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    private $model;

    /**
     * @var \TestModelWithSoftDelete|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    private $modelWithSoftDelete;

    /**
     * @var \Laravel\Crud\Repository\EloquentCrudRepository
     */
    private $repository;

    /**
     * @var \Laravel\Crud\Repository\EloquentCrudRepository
     */
    private $repositoryWithSoftDelete;

    /**
     * @var \TestDatabase
     */
    private $database;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->database = new TestDatabase();
        $this->model = new TestModel();
        $this->modelWithSoftDelete = new TestModelWithSoftDelete();
        $this->repository = new EloquentCrudRepository($this->model);
        $this->repositoryWithSoftDelete = new EloquentCrudRepository($this->modelWithSoftDelete);

        $this->database->createTables();
        $this->database->insertModels();
    }

    public function testAllModels()
    {
        $this->assertCount(2, $this->repositoryWithSoftDelete->all());
        $this->assertCount(2, $this->repository->all());
    }

    public function testAllWithTrashedModels()
    {
        $this->assertCount(5, $this->repositoryWithSoftDelete->allWithTrashed());
        $this->assertCount(2, $this->repository->allWithTrashed());
    }

    public function testAllOnlyTrashedModels()
    {
        $this->assertCount(3, $this->repositoryWithSoftDelete->allTrashed());
        $this->assertCount(0, $this->repository->allTrashed());
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindModel()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->find(1));

        try {
            $this->repositoryWithSoftDelete->find(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        $this->assertNotNull($this->repository->find(1));

        try {
            $this->repository->find(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindWithTrashedModel()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->findWithTrashed(1));
        $this->assertNotNull($this->repositoryWithSoftDelete->findWithTrashed(3));

        $this->assertNotNull($this->repository->findWithTrashed(1));

        try {
            $this->repository->findWithTrashed(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindOnlyTrashedModel()
    {
        try {
            $this->repositoryWithSoftDelete->findTrashed(1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }
        $this->assertNotNull($this->repositoryWithSoftDelete->findTrashed(5));

        try {
            $this->assertNotNull($this->repository->findTrashed(1));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindByModel()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->findBy('id', 1));

        try {
            $this->repositoryWithSoftDelete->findBy('id', 3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        $this->assertNotNull($this->repository->findBy('id', 1));

        try {
            $this->repository->findBy('id', 3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindByWithTrashedModel()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->findByWithTrashed('id', 1));
        $this->assertNotNull($this->repositoryWithSoftDelete->findByWithTrashed('id', 3));

        $this->assertNotNull($this->repository->findByWithTrashed('id', 1));

        try {
            $this->repository->findByWithTrashed('id', 3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFindByOnlyTrashedModel()
    {
        try {
            $this->repositoryWithSoftDelete->findByTrashed('id', 1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }
        $this->assertNotNull($this->repositoryWithSoftDelete->findByTrashed('id', 5));

        try {
            $this->repository->findByTrashed('id', 1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    public function testNewModel()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->newModel());
        $this->assertFalse($this->repositoryWithSoftDelete->newModel()->exists);

        $this->assertNotNull($this->repository->newModel());
        $this->assertFalse($this->repository->newModel()->exists);
    }

    public function testNewModelWithParams()
    {
        $this->assertNotNull($this->repositoryWithSoftDelete->newModel(['msg' => 'test']));
        $this->assertFalse($this->repositoryWithSoftDelete->newModel(['msg' => 'test'])->exists);
        $this->assertEquals('test', $this->repositoryWithSoftDelete->newModel(['msg' => 'test'])->msg);

        try {
            $this->repositoryWithSoftDelete->newModel(['dontExist' => 'test']);
        } catch (\Illuminate\Database\Eloquent\MassAssignmentException $e) {
            $this->assertEquals('', $e->getMessage());
        }

        $this->assertNotNull($this->repository->newModel(['msg' => 'test']));
        $this->assertFalse($this->repository->newModel(['msg' => 'test'])->exists);
        $this->assertEquals('test', $this->repository->newModel(['msg' => 'test'])->msg);

        try {
            $this->repository->newModel(['dontExist' => 'test']);
        } catch (\Illuminate\Database\Eloquent\MassAssignmentException $e) {
            $this->assertEquals('', $e->getMessage());
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testFormatModel()
    {
        $model = $this->repositoryWithSoftDelete->find(1);
        $this->assertArrayHasKey('id', $this->repositoryWithSoftDelete->formatModel($model));
        $this->assertArrayNotHasKey('dontExist', $this->repositoryWithSoftDelete->formatModel($model));

        $model = $this->repository->find(1);
        $this->assertArrayHasKey('id', $this->repository->formatModel($model));
        $this->assertArrayNotHasKey('dontExist', $this->repository->formatModel($model));
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testCreate()
    {
        $model = $this->repositoryWithSoftDelete->create([]);
        $this->assertNotNull($model);
        $this->assertTrue($model->exists);

        $model = $this->repository->create([]);
        $this->assertNotNull($model);
        $this->assertTrue($model->exists);
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testCreateWithParams()
    {
        $model = $this->repositoryWithSoftDelete->create(['msg' => 'test']);
        $this->assertNotNull($model);
        $this->assertTrue($model->exists);

        $model = $this->repository->create(['msg' => 'test']);
        $this->assertNotNull($model);
        $this->assertTrue($model->exists);
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testUpdate()
    {
        $model = $this->repositoryWithSoftDelete->find(1);
        $this->assertNull($model->msg);
        $model = $this->repositoryWithSoftDelete->update(1, ['msg' => 'test']);
        $this->assertNotNull($model->msg);

        $model = $this->repository->find(1);
        $this->assertNull($model->msg);
        $model = $this->repository->update(1, ['msg' => 'test']);
        $this->assertNotNull($model->msg);
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     */
    public function testUpdateNotFound()
    {
        try {
            $this->repositoryWithSoftDelete->update(3, []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        try {
            $this->repository->update(3, []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testDelete()
    {
        $this->assertTrue($this->repositoryWithSoftDelete->delete(1));
        $this->assertNotNull($this->repositoryWithSoftDelete->findTrashed(1));

        $this->assertTrue($this->repository->delete(1));
        $this->assertCount(1, $this->repository->allWithTrashed());
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testDeleteNotFound()
    {
        try {
            $this->repositoryWithSoftDelete->delete(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        try {
            $this->repository->delete(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testForceDelete()
    {
        $this->assertNull($this->repositoryWithSoftDelete->forceDelete(1));
        $this->assertNull($this->repositoryWithSoftDelete->findByTrashed('id', 1, '=', false));
        $this->assertNull($this->repositoryWithSoftDelete->forceDelete(3));
        $this->assertNull($this->repositoryWithSoftDelete->findByTrashed('id', 3, '=', false));

        $this->assertTrue($this->repository->forceDelete(1));
        $this->assertNull($this->repository->findByTrashed('id', 1, '=', false));
        try {
            $this->repository->forceDelete(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testForceDeleteNotFound()
    {
        try {
            $this->repositoryWithSoftDelete->delete(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        try {
            $this->repository->delete(3);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModel].');
        }
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testRestore()
    {
        $this->assertTrue($this->repositoryWithSoftDelete->restore(3));
        $this->assertNotNull($this->repositoryWithSoftDelete->find(3));

        $this->assertFalse($this->repository->restore(1));
    }

    /**
     * @throws \Laravel\Crud\Exception\AccessDeniedException
     * @throws \Exception
     */
    public function testRestoreNotFound()
    {
        try {
            $this->repositoryWithSoftDelete->restore(1);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertEquals($e->getMessage(), 'No query results for model [TestModelWithSoftDelete].');
        }

        $this->assertFalse($this->repository->restore(3));
    }

    public function testPaginate()
    {
        $paginationData = $this->repositoryWithSoftDelete->paginate($this->modelWithSoftDelete->where('id', 1), 1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(1, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);

        $paginationData = $this->repository->paginate($this->model->where('id', 1), 1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(1, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);
    }

    public function testPaginateCollection()
    {
        $paginationData = $this->repositoryWithSoftDelete->paginateCollection($this->repositoryWithSoftDelete->allWithTrashed(), 1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(2, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(5, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(2, $paginationData->pages);

        $paginationData = $this->repository->paginateCollection($this->repository->allWithTrashed(), 1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(2, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);
    }

    public function testPagination()
    {
        $paginationData = $this->repositoryWithSoftDelete->pagination(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(2, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);

        $paginationData = $this->repository->pagination(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(2, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);
    }

    public function testPaginationWithTrashed()
    {
        $paginationData = $this->repositoryWithSoftDelete->paginationWithTrashed(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(2, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(5, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(2, $paginationData->pages);

        $paginationData = $this->repository->paginationWithTrashed(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(2, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);
    }

    public function testPaginationOnlyTrashed()
    {
        $paginationData = $this->repositoryWithSoftDelete->paginationOnlyTrashed(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(3, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(1, $paginationData->pages);

        $paginationData = $this->repository->paginationOnlyTrashed(1, 3);
        $this->assertObjectHasAttribute('result', $paginationData);
        $this->assertCount(0, $paginationData->result);
        $this->assertObjectHasAttribute('total', $paginationData);
        $this->assertEquals(0, $paginationData->total);
        $this->assertObjectHasAttribute('page', $paginationData);
        $this->assertEquals(1, $paginationData->page);
        $this->assertObjectHasAttribute('pages', $paginationData);
        $this->assertEquals(0, $paginationData->pages);
    }
}
