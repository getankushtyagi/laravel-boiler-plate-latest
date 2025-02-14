<?php

namespace App\Repositories\Database;

use App\Models\User;
use App\Repositories\Database\Traits\DatabaseRepositoryTrait;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Arr;

class UserRepository implements UserRepositoryInterface
{
    use DatabaseRepositoryTrait;

    protected $model = User::class;

    protected function applyFilters($query, $inputs)
    {
        if ($searchKey = Arr::get($inputs, 'q')) {
            $query->where('name', 'LIKE', "%$searchKey%")
                ->orWhere('mobile', 'LIKE', "%$searchKey%")
                ->orWhere('uuid', 'LIKE', "%$searchKey%");
        }

        return $query;
    }

    public function getAllUsers($user, $inputs, $with = [], $withCount = [])
    {
        $query = $this->query();
        $query = $this->applyFilters($query, $inputs);

        return $this->getResults($query, $inputs, $with, $withCount);
    }
}
