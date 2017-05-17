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
	Route::post('logout', 'Auth\LoginController@logout')->middleware('auth:api');
});

// TODO Change this to proper middleware group later on
Route::resource('dishes', 'DishController', ['except' => ['create', 'edit']]);
Route::resource('kitchens', 'KitchenController');

// This route group applies the "web" middleware group to every route
// it contains. The "web" middleware group is defined in your HTTP
// kernel and includes session state, CSRF protection, and more.
Route::post('login', 'Auth\LoginController@login');
Route::post('refresh-token', 'Auth\LoginController@refreshToken');

Route::get('test', function() {
	echo 123;
	$s3 = Storage::disk('s3');
	$s3->put('myFile.txt', 'this is a sample text to upload to s3', 'public');
});