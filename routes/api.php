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
	Route::get('logged-in', 'UserController@getLoggedInUser');
// TODO Change this to proper middleware group later on
	Route::resource('dishes/{dishId}/rating', 'DishRatingController', ['except' => ['index', 'show', 'edit']]);
	Route::resource('dishes', 'DishController', ['only' => ['store', 'destroy', 'update']]);
	Route::resource('kitchens', 'KitchenController', ['only' => ['store', 'destroy', 'update']]);
	Route::resource('media', 'MediaController', ['only' => 'store']);
	Route::resource('reactions', 'ReactionController',
		['parameters' => ['reactions' => 'like_id'], 'only' => ['store', 'destroy']]);

	Route::get('users/getMyKitchens', 'UserController@getMyKitchens');

	Route::post('kitchens/{id}/admins', 'KitchenController@addAdmin');
	Route::delete('kitchens/{id}/admins', 'KitchenController@removeAdmin');
});
// TODO Uncomment below 2 lines when auth is all ready
Route::resource('dishes', 'DishController', ['only' => ['index', 'show']]);
Route::resource('kitchens', 'KitchenController', ['only' => ['index', 'show']]);

Route::get('dishes/{dishId}/rating/avg', 'DishRatingController@getAvg');
Route::resource('dishes/{dishId}/rating', 'DishRatingController', ['only' => ['index', 'show']]);

Route::post('reactions/getReactions',
	['uses' => 'ReactionController@index', 'as' => 'reactions.getReactions']);

Route::post('refresh-token', 'Auth\LoginController@refreshToken');
Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');
Route::post('auth/facebook', 'Auth\LoginController@facebook');
Route::post('auth/google', 'Auth\LoginController@google');

Route::get('kitchens/{id}/admins', 'KitchenController@getAdmins');

Route::get('users/list', 'UserController@index');

// Password Reset Routes...
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->get('password/reset/{token?}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

// Checkout
Route::post('charge-payment', 'CheckoutController@charge');