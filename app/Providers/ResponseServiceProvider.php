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
        Response::macro('success', function ($data = '', $message = '') {
            return Response::json([
              'status' => 'success',
              'data' => $data,
              'message' => $message == '' ? trans('api.defaultSuccess') : $message
            ], 200);
        });

        // fail
        Response::macro('fail', function ($message = '') {
            return Response::json([
              'status' => 'failure',
              'message' => $message == '' ? trans('api.defaultFailure') : $message
            ], 500);
        });

        // unautherised
        Response::macro('unauth', function ($message = '') {
            return Response::json([
              'status' => 'failure',
              'message' => $message == '' ? trans('auth.unauthrised') : $message
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