# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install

# Run full test suite
vendor/bin/phpunit --verbose --testdox

# Run a single test method
vendor/bin/phpunit tests/EloquentCrudTest.php --filter testMethodName

# Check code style (dry run)
vendor/bin/php-cs-fixer fix --dry-run

# Apply code style fixes
vendor/bin/php-cs-fixer fix
```

## Architecture

This is a PHP library implementing the Repository pattern over Laravel Eloquent.

**Layer structure:**
- `src/Repository/CrudRepository.php` — Interface defining all public CRUD contract
- `src/Repository/Eloquent/CrudRepository.php` — Concrete implementation wrapping an Eloquent `Model`
- `src/Exception/AccessDeniedException.php` — Thrown by access control checks (HTTP 403, stores constructor args)

**Usage pattern:** Consumers subclass `Eloquent\CrudRepository`, inject their Eloquent model via `__construct(Model $model)`, and optionally override the `can*` methods for authorization logic.

**Access control:** Every public mutation and retrieval method calls a `checkCan*()` guard first (fail-fast). The guards call the corresponding `can*()` method (default: always `true`) and throw `AccessDeniedException` on denial. To restrict access, override `canShow`, `canCreate`, `canUpdate`, `canDelete`, or `canRestore` in a subclass.

**SoftDeletes support:** The implementation detects whether the wrapped model uses the `SoftDeletes` trait via `class_uses()` at runtime. Every major retrieval method (`all`, `find`, `findBy`, `pagination`) has three variants: normal, `WithTrashed`, and `Trashed`/`OnlyTrashed`.

**Pagination:** Returns a plain `object` with `result` (Collection), `total` (int), `page` (int), and `pages` (int). Two helpers exist: `paginate(Builder, page, limit)` for query builders and `paginateCollection(Collection, page, limit)` for in-memory collections.

**Model formatting:** `formatModel(Model)` converts a model to an array. If the model has a `select` attribute, only those fields are included.

## Tests

Tests use an SQLite in-memory database (configured in `phpunit.xml` via `DB_CONNECTION=sqlite`). Test fixtures live in `tests/`: `TestModel` (basic), `TestModelWithSoftDelete` (with `SoftDeletes`), and `TestDatabase` (schema setup with pre-seeded rows including 3 pre-trashed soft-delete records).
