<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * Get all records based on optional input parameters.
     *
     * @param array<string, mixed> $inputs Additional query parameters
     * @return Collection<int, Model>
     */
    public function all(array $inputs = []): Collection;

    /**
     * Get all records with pagination.
     *
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(array $inputs): LengthAwarePaginator;

    /**
     * Get first record matching the conditions.
     *
     * @param string $column Column to search by
     * @param mixed $value Value to match
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @param bool $throws Whether to throw an exception if not found
     * @param string|null $pluck Optional column to pluck from the result
     * @return Model|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstWhere(string $column, mixed $value, array $moreWhere = [], bool $throws = true, ?string $pluck = null): ?Model;

    /**
     * Create a new model instance.
     *
     * @param array<string, mixed> $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Create multiple model instances.
     *
     * @param array<int, array<string, mixed>> $multipleAttributes
     * @return Collection<int, Model>
     */
    public function createMany(array $multipleAttributes): Collection;

    /**
     * Create a related model.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<string, mixed> $fields Fields for the related model
     * @return Model
     */
    public function createRelationally(Model $model, string $relation, array $fields): Model;

    /**
     * Create multiple related models.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, array<string, mixed>> $manyFields Fields for the related models
     * @return Collection<int, Model>
     */
    public function createManyRelationally(Model $model, string $relation, array $manyFields): Collection;

    /**
     * Delete models matching the conditions.
     *
     * @param string $column Column to search by
     * @param string $value Value to match
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @param bool $throw Whether to throw an exception if no records found
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteWhere(string $column, string $value, array $moreWhere = [], bool $throw = false): bool;

    /**
     * Delete related models.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @return bool
     */
    public function deleteRelationally(Model $model, string $relation): bool;

    /**
     * Delete models where column value is not in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of deleted records
     */
    public function deleteWhereNotIn(string $column, array $range, array $moreWhere = []): int;

    /**
     * Delete models where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of deleted records
     */
    public function deleteWhereIn(string $column, array $range, array $moreWhere = []): int;

    /**
     * Update models matching the conditions.
     *
     * @param string $column Column to search by
     * @param string $value Value to match
     * @param array<string, mixed> $attributes Update attributes
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of updated records
     */
    public function updateWhere(string $column, string $value, array $attributes, array $moreWhere = []): int;

    /**
     * Update models where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $attributes Update attributes
     * @return int Number of updated records
     */
    public function updateWhereIn(string $column, array $range, array $attributes): int;

    /**
     * Update a specific model instance.
     *
     * @param Model $model Model to update
     * @param array<string, mixed> $attributes Update attributes
     * @return bool
     */
    public function update(Model $model, array $attributes): bool;

    /**
     * Get records matching the conditions.
     *
     * @param string $column Column to search by
     * @param mixed $value Value to match
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @param string|null $pluck Optional column to pluck from results
     * @param array<string, mixed> $inputs Additional query parameters
     * @return Collection<int, Model>
     */
    public function getWhere(string $column, mixed $value, array $moreWhere = [], ?string $pluck = null, array $inputs = []): Collection;

    /**
     * Get a specific model by ID.
     *
     * @param int $id Model ID
     * @param array<string>|null $related Related models to eager load
     * @param bool $throw Whether to throw an exception if not found
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id, ?array $related = null, bool $throw = true): Model;

    /**
     * Get paginated records matching the conditions.
     *
     * @param string $column Column to search by
     * @param mixed $value Value to match
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getWherePaginated(string $column, mixed $value, array $moreWhere = [], array $inputs = []): LengthAwarePaginator;

    /**
     * Find existing record or create new one.
     *
     * @param array<string, mixed> $conditions Search conditions
     * @param array<string, mixed> $fields Fields for new record if not found
     * @return Model
     */
    public function firstOrCreate(array $conditions, array $fields): Model;

    /**
     * Check if record exists.
     *
     * @param string $column Column to search by
     * @param mixed $value Value to match
     * @param array<string, mixed>|null $moreWhere Additional where conditions
     * @param bool $throw Whether to throw an exception if not found
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function exists(string $column, mixed $value, ?array $moreWhere = null, bool $throw = true): bool;

    /**
     * Throw model not found exception.
     *
     * @param string|null $message Custom error message
     * @return never
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function throwModelNotFound(?string $message = null): never;

    /**
     * Update existing related model or create new one.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<string, mixed> $conditions Search conditions
     * @param array<string, mixed> $fields Update or create fields
     * @return Model
     */
    public function updateOrCreateRelationally(Model $model, string $relation, array $conditions, array $fields): Model;

    /**
     * Get records where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $inputs Additional query parameters
     * @return Collection<int, Model>
     */
    public function getWhereIn(string $column, array $range, array $inputs): Collection;

    /**
     * Get paginated records where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getWhereInPaginated(string $column, array $range, array $inputs): LengthAwarePaginator;

    /**
     * Attach records to a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed> $attributes IDs or additional pivot attributes
     * @return mixed
     */
    public function attach(Model $model, string $relation, array $attributes): mixed;

    /**
     * Detach records from a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed>|null $attributes IDs to detach (null for all)
     * @return mixed
     */
    public function detach(Model $model, string $relation, ?array $attributes = null): mixed;

    /**
     * Sync records in a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed> $attributes IDs or additional pivot attributes
     * @return array<string, mixed> Array of sync results
     */
    public function sync(Model $model, string $relation, array $attributes): array;

    /**
     * Associate a model with a belongs-to relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param Model|int $attributes Related model or its ID
     * @return Model
     */
    public function associate(Model $model, string $relation, Model|int $attributes): Model;
}
