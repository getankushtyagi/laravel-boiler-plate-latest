<?php

namespace App\Http\Controllers\api\v1;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Transformers\UserTransformer;
use Arr;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Retrieve list of users",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index()
    {
        $inputs = request()->all();
        $user = auth()->user();
        $userdata = app()->call(
            [$this->userService, 'getAllUsers'],
            ['user' => $user, 'inputs' => $inputs]
        );

        if(!is_null($userdata)) {
            UserRegistered::dispatch($userdata);
        }
        if (Arr::get($inputs, 'all')) {
            return response()->success($this->getTransformedData($userdata, new UserTransformer));
        }

        return response()->withMeta($this->getTransformedDataWithPagination($userdata, new UserTransformer));

    }
}
