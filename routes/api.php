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
//	Route::resource('dishes/{dishId}/rating', 'Dish\DishRatingController', ['except' => ['index', 'show', 'create', 'edit']]);
//	Route::resource('dishes', 'Dish\DishController', ['only' => ['store', 'destroy', 'update']]);
// TODO Uncomment below 2 lines when auth is all ready
//	Route::resource('kitchens', 'KitchenController', ['only' => ['store', 'destroy', 'update']]);
//	Route::resource('media', 'MediaController', ['only' => 'store']);

//	Route::get('user/getMyKitchens', 'UserController@getMyKitchens');
});
// TODO Uncomment below 2 lines when auth is all ready
//Route::resource('dishes', 'Dish\DishController', ['only' => ['index', 'show']]);
//Route::resource('kitchens', 'KitchenController', ['only' => ['index', 'show']]);

Route::get('dishes/{dishId}/rating/avg', 'DishRatingController@getAvg');
Route::resource('dishes/{dishId}/rating', 'DishRatingController', ['only' => ['index', 'show']]);

Route::resource('dishes', 'DishController');
Route::resource('kitchens', 'KitchenController');
Route::resource('media', 'MediaController', ['only' => 'store']);


Route::post('reactions/getReactions',
	['uses' => 'ReactionController@index', 'as' => 'reactions.getReactions']);
Route::resource('reactions', 'ReactionController',
	['parameters' => ['reactions' => 'like_id'], 'only' => ['store', 'destroy']]);

Route::post('refresh-token', 'Auth\LoginController@refreshToken');
Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');
Route::post('auth/facebook', 'Auth\LoginController@facebook');
Route::post('auth/google', 'Auth\LoginController@google');\

Route::get('kitchens/{id}/admins', 'KitchenController@getAdmins');
Route::post('kitchens/{id}/admins', 'KitchenController@addAdmin');
Route::delete('kitchens/{id}/admins', 'KitchenController@removeAdmin');

Route::get('users/getMyKitchens', 'UserController@getMyKitchens');
Route::get('users/list', 'UserController@index');