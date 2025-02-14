<?php

namespace App\Providers;

use Arr;
use Illuminate\Support\ServiceProvider;
use Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\Database\UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data, $status = 200, $code = null) {
            $code = $code ?: $status;

            $response = [
                'success' => true,
                'code' => $code,
            ];

            if (is_array($data)) {
                $response += [
                    'data' => $data,
                ];
            } else {
                $response += [
                    'message' => $data,
                ];
            }

            return Response::json($response, $status);
        });

        Response::macro('withMeta', function ($data, $status = 200, $code = null) {
            $code = $code ?: $status;

            $unwrappedData = isset($data['data']) ? $data['data'] : $data;

            return Response::json(
                [
                    'success' => true,
                    'code'    => $code,
                    'data'    => $unwrappedData,
                    'meta'    => Arr::get($data, 'meta'),
                ],
                $status
            );
        });


        Response::macro('error', function ($errors, $status = 400, $code = null) {
            $code = $code ?: $status;

            $response = [
                'success' => false,
                'code' => $code,
            ];

            if (is_array($errors)) {
                $response += [
                    'errors' => $errors,
                ];
            } else {
                $response += [
                    'message' => $errors,
                ];
            }

            return Response::json($response, $status);
        });

        Response::macro('noContent', function () {
            Response::json(['status' => 204], 204);
        });
    }
}
