<?php

namespace App\Repositories\Database\Traits;

use App\Exceptions\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

trait DatabaseRepositoryTrait
{
    /**
     * Get query builder instance for the model.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {
        return call_user_func("$this->model::query");
    }

    /**
     * Get all records based on optional input parameters.
     *
     * @param array<string, mixed> $inputs Additional query parameters
     * @return Collection<int, Model>
     */
    public function all(array $inputs = []): Collection
    {
        $query = $this->query();

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        return $query->get();
    }

    /**
     * Get all records with pagination.
     *
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(array $inputs): LengthAwarePaginator
    {
        $query = $this->query();

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        return $query->paginate(Arr::get($inputs, 'per_page', 15));
    }

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
    public function firstWhere(string $column, mixed $value, array $moreWhere = [], bool $throws = true, ?string $pluck = null): ?Model
    {
        $query = $this->query();
        $query->where($column, $value);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        $model = $pluck ? $query->pluck($pluck) : $query->first();

        if ($throws && !$model) {
            $this->throwModelNotFound('Model doesn\'t exist.');
        }

        return $model;
    }

    /**
     * Create a new model instance.
     *
     * @param array<string, mixed> $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    /**
     * Create multiple model instances.
     *
     * @param array<int, array<string, mixed>> $multipleAttributes
     * @return Collection<int, Model>
     */
    public function createMany(array $multipleAttributes): Collection
    {
        return collect($this->query()->createMany($multipleAttributes));
    }

    /**
     * Create a related model.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<string, mixed> $fields Fields for the related model
     * @return Model
     */
    public function createRelationally(Model $model, string $relation, array $fields): Model
    {
        return $model->$relation()->create($fields);
    }

    /**
     * Create multiple related models.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, array<string, mixed>> $manyFields Fields for the related models
     * @return Collection<int, Model>
     */
    public function createManyRelationally(Model $model, string $relation, array $manyFields): Collection
    {
        return collect($model->$relation()->createMany($manyFields));
    }

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
    public function deleteWhere(string $column, string $value, array $moreWhere = [], bool $throw = false): bool
    {
        $query = $this->query();
        $query->where($column, $value);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        $deleteCount = $query->delete();

        if (!$deleteCount && $throw) {
            $this->throwModelNotFound('Model doesn\'t exist.');
        }

        return (bool) $deleteCount;
    }

    /**
     * Delete models where column value is not in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of deleted records
     */
    public function deleteWhereNotIn(string $column, array $range, array $moreWhere = []): int
    {
        $query = $this->query();
        $query->whereNotIn($column, $range);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        return $query->delete();
    }

    /**
     * Delete models where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of deleted records
     */
    public function deleteWhereIn(string $column, array $range, array $moreWhere = []): int
    {
        $query = $this->query();
        $query->whereIn($column, $range);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        return $query->delete();
    }

    /**
     * Delete related models.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @return bool
     */
    public function deleteRelationally(Model $model, string $relation): bool
    {
        return (bool) $model->$relation()->delete();
    }

    /**
     * Update models matching the conditions.
     *
     * @param string $column Column to search by
     * @param string $value Value to match
     * @param array<string, mixed> $attributes Update attributes
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @return int Number of updated records
     */
    public function updateWhere(string $column, string $value, array $attributes, array $moreWhere = []): int
    {
        $query = $this->query();
        $query->where($column, $value);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        return $query->update($attributes);
    }

    /**
     * Update models where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $attributes Update attributes
     * @return int Number of updated records
     */
    public function updateWhereIn(string $column, array $range, array $attributes): int
    {
        return $this->query()->whereIn($column, $range)->update($attributes);
    }

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
    public function getWhere(string $column, mixed $value, array $moreWhere = [], ?string $pluck = null, array $inputs = []): Collection
    {
        $query = $this->query();
        $query->where($column, $value);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        return $pluck ? $query->pluck($pluck) : $query->get();
    }

    /**
     * Get a specific model by ID.
     *
     * @param int $id Model ID
     * @param array<string>|null $related Related models to eager load
     * @param bool $throw Whether to throw an exception if not found
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id, ?array $related = null, bool $throw = true): Model
    {
        $query = $this->query();

        if ($related) {
            $query->with($related);
        }

        $model = $query->find($id);

        if ($throw && !$model) {
            $this->throwModelNotFound('Model not found.');
        }

        return $model;
    }

    /**
     * Get paginated records matching the conditions.
     *
     * @param string $column Column to search by
     * @param mixed $value Value to match
     * @param array<string, mixed> $moreWhere Additional where conditions
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getWherePaginated(string $column, mixed $value, array $moreWhere = [], array $inputs = []): LengthAwarePaginator
    {
        $query = $this->query();
        $query->where($column, $value);

        foreach ($moreWhere as $condition) {
            $query->where($condition);
        }

        return $query->paginate(Arr::get($inputs, 'per_page', 15));
    }

    /**
     * Find existing record or create new one.
     *
     * @param array<string, mixed> $conditions Search conditions
     * @param array<string, mixed> $fields Fields for new record if not found
     * @return Model
     */
    public function firstOrCreate(array $conditions, array $fields): Model
    {
        return $this->query()->firstOrCreate($conditions, $fields);
    }

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
    public function exists(string $column, mixed $value, ?array $moreWhere = null, bool $throw = true): bool
    {
        $query = $this->query();
        $query->where($column, $value);

        if ($moreWhere) {
            $query->where($moreWhere);
        }

        $exists = $query->exists();

        if ($throw && !$exists) {
            $this->throwModelNotFound('Model doesn\'t exist.');
        }

        return $exists;
    }

    /**
     * Throw model not found exception.
     *
     * @param string|null $message Custom error message
     * @return never
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function throwModelNotFound(?string $message = null): never
    {
        throw new \Illuminate\Database\Eloquent\ModelNotFoundException($message);
    }

    /**
     * Update existing related model or create new one.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<string, mixed> $conditions Search conditions
     * @param array<string, mixed> $fields Update or create fields
     * @return Model
     */
    public function updateOrCreateRelationally(Model $model, string $relation, array $conditions, array $fields): Model
    {
        return $model->$relation()->updateOrCreate($conditions, $fields);
    }

    /**
     * Get records where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $inputs Additional query parameters
     * @return Collection<int, Model>
     */
    public function getWhereIn(string $column, array $range, array $inputs): Collection
    {
        $query = $this->query();
        $query->whereIn($column, $range);

        if ($moreWhere = Arr::get($inputs, 'morewhere')) {
            foreach ($moreWhere as $condition) {
                $query->where($condition);
            }
        }

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        return $query->get();
    }

    /**
     * Get paginated records where column value is in the given range.
     *
     * @param string $column Column to check
     * @param array<int, mixed> $range Array of values
     * @param array<string, mixed> $inputs Pagination and query parameters
     * @return LengthAwarePaginator
     */
    public function getWhereInPaginated(string $column, array $range, array $inputs): LengthAwarePaginator
    {
        $query = $this->query();
        $query->whereIn($column, $range);

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        return $query->paginate(Arr::get($inputs, 'per_page', 15));
    }

    /**
     * Attach records to a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed> $attributes IDs or additional pivot attributes
     * @return mixed
     */
    public function attach(Model $model, string $relation, array $attributes): mixed
    {
        return $model->$relation()->attach($attributes);
    }

    /**
     * Detach records from a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed>|null $attributes IDs to detach (null for all)
     * @return mixed
     */
    public function detach(Model $model, string $relation, ?array $attributes = null): mixed
    {
        return $model->$relation()->detach($attributes);
    }

    /**
     * Sync records in a many-to-many relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param array<int, mixed> $attributes IDs or additional pivot attributes
     * @return array<string, mixed>
     */
    public function sync(Model $model, string $relation, array $attributes): array
    {
        return $model->$relation()->sync($attributes);
    }

    /**
     * Associate a model with a belongs-to relationship.
     *
     * @param Model $model Parent model
     * @param string $relation Relation name
     * @param Model|int|array<string, mixed> $attributes Related model, its ID, or attributes to create
     * @return Model The updated parent model
     */
    public function associate(Model $model, string $relation, Model|int|array $attributes): Model
    {
        if (is_array($attributes)) {
            $related = $model->$relation()->create($attributes);
        } elseif ($attributes instanceof Model) {
            $related = $attributes;
        } else {
            $related = $this->get($attributes);
        }

        $model->$relation()->associate($related);
        $model->save();

        return $model;
    }

    /**
     * Update the model by the given attributes.
     *
     * @param Model|int $model Model instance or ID
     * @param array<string, mixed> $attributes Update attributes
     * @return bool
     */
    public function update(Model|int $model, array $attributes): bool
    {
        if ($model instanceof Model) {
            return $model->update($attributes);
        }

        return $this->get($model)->update($attributes);
    }

    /**
     * Get the results of given query.
     *
     * @param  array  $inputs  = []
     * @param  array  $with  = []
     * @param  array  $withCount  = []
     * @param  bool  $throw  = false
     * @return mixed
     */
    public function getResults(
        QueryBuilder $query,
        array $inputs = [],
        array $with = [],
        array $withCount = [],
        bool $throw = false
    ): Collection|LengthAwarePaginator {
        $query->with($with);

        $query->withCount($withCount);

        if (!Arr::has($inputs, 'oldest')) {
            $query->latest();
        }

        $results = Arr::has($inputs, 'all')
            ? $query->get()
            : $query->paginate(Arr::get($inputs, 'per_page', 15));

        if ($throw && $results->isEmpty()) {
            $this->throwModelNotFound('Models doesn\'t exists.');
        }

        return $results;
    }
}
