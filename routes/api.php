<?php

use Illuminate\Support\Facades\Storage;
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
	Route::post('logout', 'Auth\LoginController@logout');
	Route::post('refresh-token', 'Auth\LoginController@refreshToken');
// TODO Change this to proper middleware group later on
	Route::resource('dishes/{dishId}/rating', 'Dish\DishRatingController', ['except' => ['index', 'show', 'create', 'edit']]);
	Route::resource('dishes', 'DishController', ['except' => ['index', 'show', 'create', 'edit']]);
	Route::resource('kitchens', 'KitchenController', ['except' => ['index', 'show', 'create', 'edit']]);
	Route::resource('media', 'MediaController', ['only' => 'store']);
});
//TODO: list
Route::resource('dishes', 'DishController', ['only' => ['index', 'show']]);
Route::resource('kitchens', 'KitchenController', ['only' => ['index', 'show']]);
Route::get('dishes/{dishId}/rating/avg', 'Dish\DishRatingController@getAvg');
Route::resource('dishes/{dishId}/rating', 'Dish\DishRatingController', ['only' => ['index', 'show']]);

Route::post('login', 'Auth\LoginController@login');
Route::post('auth/facebook', 'Auth\LoginController@facebook');
Route::post('auth/google', 'Auth\LoginController@google');
