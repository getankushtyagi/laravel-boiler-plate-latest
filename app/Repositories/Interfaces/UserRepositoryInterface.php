<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllUsers($inputs, $user , $with = [], $withCount = []);

    // Add other data access methods as needed
}
