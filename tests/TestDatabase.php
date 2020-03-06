<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

class TestDatabase
{
    public function __construct()
    {
        $capsule = new Manager();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__.'/testing.sqlite',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function createTables(): void
    {
        Manager::schema('default')->dropIfExists('test_models');
        Manager::schema('default')->create('test_models', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('msg')->nullable();
            $table->timestamps();
        });
        Manager::schema('default')->dropIfExists('test_model_with_soft_deletes');
        Manager::schema('default')->create('test_model_with_soft_deletes', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('msg')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * @throws \Exception
     */
    public function insertModels(): void
    {
        TestModel::create();
        TestModel::create();

        TestModelWithSoftDelete::create();
        TestModelWithSoftDelete::create();
        $model = TestModelWithSoftDelete::create();
        $model->delete();
        $model = TestModelWithSoftDelete::create();
        $model->delete();
        $model = TestModelWithSoftDelete::create();
        $model->delete();
    }
}
