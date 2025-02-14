<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;



class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($user, $inputs): LengthAwarePaginator
    {
        // Add any business logic if needed
        return $this->userRepository->getAllUsers($user, $inputs);
    }

}
