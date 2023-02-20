# Eloquent CRUD Repository

[![Build Status](https://travis-ci.com/JoseVte/eloquent-crud-repository.svg?branch=master)](https://travis-ci.com/JoseVte/eloquent-crud-repository)
[![Latest Stable Version](http://poser.pugx.org/josrom/eloquent-crud-repository/v)](https://packagist.org/packages/josrom/eloquent-crud-repository)
[![Total Downloads](http://poser.pugx.org/josrom/eloquent-crud-repository/downloads)](https://packagist.org/packages/josrom/eloquent-crud-repository)
[![License](http://poser.pugx.org/josrom/eloquent-crud-repository/license)](https://packagist.org/packages/josrom/eloquent-crud-repository)

### Introduction

EloquentCrudRepository provides a well tested and complete base to create more model repositories using the repository pattern with Eloquent as ORM.

## Public methods available

|Name|Parameters|Return|
|----|----------|------|
|all|array $with = []|\Illuminate\Database\Eloquent\Collection|
|allWithTrashed|array $with = []|\Illuminate\Database\Eloquent\Collection|
|allTrashed|array $with = []|\Illuminate\Database\Eloquent\Collection|
|find|int $id, array $with = []|\Illuminate\Database\Eloquent\Model|
|findWithTrashed|int $id, array $with = []|\Illuminate\Database\Eloquent\Model|
|findTrashed|int $id, array $with = []|\Illuminate\Database\Eloquent\Model|
|findBy|string $field, mixed $value, string $comparison = '=', bool $strict = true, array $with = []|\Illuminate\Database\Eloquent\Model|
|findByWithTrashed|string $field, mixed $value, string $comparison = '=', bool $strict = true, array $with = []|\Illuminate\Database\Eloquent\Model|
|findByTrashed|string $field, mixed $value, string $comparison = '=', bool $strict = true, array $with = []|\Illuminate\Database\Eloquent\Model|
|newModel|array $params = []|\Illuminate\Database\Eloquent\Model|
|create|array $params|\Illuminate\Database\Eloquent\Model|
|update|int $id, array $params|\Illuminate\Database\Eloquent\Model|
|delete|int $id|bool|
|forceDelete|int $id|bool|
|restore|int $id|bool|
|paginate|\Illuminate\Database\Eloquent\Builder $query, int $page = 0, int $limit = 15|object|
|paginateCollection|\Illuminate\Database\Eloquent\Collection $collection, int $page = 0, int $limit = 15|object|
|pagination|int $page = 0, int $limit = 15|object|
|paginationWithTrashed|int $page = 0, int $limit = 15|object|
|paginationOnlyTrashed|int $page = 0, int $limit = 15|object|

## Protected methods available

|Name|Parameters|Return|
|----|----------|------|
|checkCanShow|\Illuminate\Database\Eloquent\Model $model|void|
|checkCanCreate|array $params|void|
|checkCanUpdate|\Illuminate\Database\Eloquent\Model $model, array $newValues|void|
|checkCanDelete|\Illuminate\Database\Eloquent\Model $model|void|
|checkCanRestore|\Illuminate\Database\Eloquent\Model $model|void|
|canShow|\Illuminate\Database\Eloquent\Model $model|bool|
|canCreate|array $params|bool|
|canUpdate|\Illuminate\Database\Eloquent\Model $model, array $newValues|bool|
|canDelete|\Illuminate\Database\Eloquent\Model $model|bool|
|canRestore|\Illuminate\Database\Eloquent\Model $model|bool|
|hasSoftDeletes| |bool|

## Installation

To get the last version of EloquentCrudRepository, simply require the project using [Composer](https://getcomposer.org/):


```bash
composer require josrom/eloquent-crud-repository
```

Instead, you may of course manually update your require block and run composer update if you so choose:

```json
{
    "require": {
        "josrom/eloquent-crud-repository": "^10.0"
    }
}
```

## License

EloquentCrudRepository is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
