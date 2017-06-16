<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // success
        Response::macro('success', function ($data = '', $returnCode = 1, $message = '') {
            return Response::json([
				'status' => 'success',
				'return_code' => $returnCode,
				'body' => $data,
				'message' => trans('api.' . $returnCode ) . $message
            ], 200);
        });

        // fail
        Response::macro('fail', function ($returnCode = 0, $message = '') {
            return Response::json([
				'status' => 'failure',
				'return_code' => $returnCode,
				'message' => trans('api.' . $returnCode ) . $message
            ], 500);
        });

        // unautherised
        Response::macro('unauth', function ($returnCode = 2, $message = '') {
            return Response::json([
				'status' => 'failure',
				'return_code' => $returnCode,
				'message' => trans('api.' . $returnCode ) . $message
            ], 401);
        });

        // not allowed
        Response::macro('notallow', function ($returnCode = 3, $message = '') {
            return Response::json([
                'status' => 'failure',
                'return_code' => $returnCode,
                'message' => trans('api.' . $returnCode ) . $message
            ], 403);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}