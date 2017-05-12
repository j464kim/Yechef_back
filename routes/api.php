<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function () {
	Route::post('logout', 'Auth\LoginController@logout')->middleware('auth:api');
});

// This route group applies the "web" middleware group to every route
// it contains. The "web" middleware group is defined in your HTTP
// kernel and includes session state, CSRF protection, and more.

// TODO Change this to proper middleware group later on
    Route::resource('dishes', 'DishController');
    Route::resource('kitchens', 'KitchenController');

// TODO Change this to proper middleware group later on
Route::resource('dishes', 'DishController');


Route::post('login', 'Auth\LoginController@login');
Route::post('refresh-token', 'Auth\LoginController@refreshToken');
