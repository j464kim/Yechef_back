<?php

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
// TODO Change this to proper middleware group later on
	Route::resource('dishes', 'DishController', ['except' => ['create', 'edit']]);
	Route::resource('kitchens', 'KitchenController');
});


Route::post('refresh-token', 'Auth\LoginController@refreshToken');
Route::post('login', 'Auth\LoginController@login');
Route::post('auth/facebook', 'Auth\LoginController@facebook');
Route::post('auth/google', 'Auth\LoginController@google');
