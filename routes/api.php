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

	Route::post('dishes/checkOwnership', 'UserController@checkOwnership');
	Route::post('kitchens/checkOwnership', 'UserController@checkOwnership');

	Route::get('users/getMyKitchensInCompactList', 'UserController@getMyKitchensInCompactList');
	Route::get('users/getMySubscriptions', 'UserController@getSubscriptions');
	Route::get('users/getMyForkedDishes', 'UserController@getForkedDishes');

	Route::post('kitchens/{id}/admins', 'KitchenController@addAdmin');
	Route::delete('kitchens/{id}/admins', 'KitchenController@removeAdmin');
	Route::resource('carts', 'CartController', ['except' => ['show']]);

	Route::get('users/getOrders', 'UserController@getOrders');
	Route::get('users/cancelOrder/{orderId}', 'Payment\OrderController@cancelOrder');

//	Update Password
	Route::post('password/update', 'Auth\UpdatePasswordController@update');

// Checkout
	Route::post('payment/charge', 'Payment\CheckoutController@charge');
	Route::resource('payment', 'Payment\PaymentController',
		['only' => ['index', 'show', 'store', 'update', 'destroy']]
	);
	Route::resource('payout', 'Payment\PayoutController',
		['only' => ['index', 'store', 'update']]
	);
	Route::get('payout/externalAccount', 'Payment\PayoutController@getExternalAccounts');
	Route::post('payout/externalAccount', 'Payment\PayoutController@createExternalAccount');
	Route::delete('payout/externalAccount/{id}', 'Payment\PayoutController@destroyExternalAccount');
	Route::post('payout/externalAccount/switchDefault', 'Payment\PayoutController@switchDefaultAccount');
	Route::put('payout/{id}/personalInfo', 'Payment\PayoutController@updatePersonalInfo');
	Route::post('payout/identity', 'Payment\PayoutController@uploadID');

	Route::get('users/checkPayout', 'UserController@checkPayout');
	Route::resource('users', 'UserController', ['only' => ['update']]);
	// user settings
	Route::get('userSetting', 'UserSettingController@show');
	Route::put('userSetting', 'UserSettingController@update');

//	My Kitchen
	Route::get('kitchens/{id}/orders', 'KitchenController@getOrders');
	Route::get('kitchens/{id}/acceptOrder/{orderId}', 'Payment\OrderController@acceptOrder');
	Route::get('kitchens/{id}/declineOrder/{orderId}', 'Payment\OrderController@declineOrder');

	// Messaging
	Route::post('sendMessage', 'MessageController@sendMessage');
	Route::resource('message', 'MessageController', ['only' => ['index', 'destroy']]);
	Route::get('myMessageRooms', 'MessageController@getRooms');
	Route::post('joinMessageRoom', 'MessageController@joinRoom');
});

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

Route::get('users/list', 'UserController@index');
Route::resource('users', 'UserController', ['only' => ['index', 'show']]);

Route::get('search/dishes', 'DishController@search');

// Password Reset Routes...
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->get('password/reset/{token?}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

$this->post('register/verify', 'Auth\RegisterController@sendEmailVerifyLink');
$this->get('register/confirm/{token}', 'Auth\RegisterController@confirmEmail');

Route::get('media/{id}/{modelName}', 'MediaController@show');
Route::resource('media', 'MediaController', ['only' => 'destroy']);

//Get User Information for User Show page
Route::get('users/{userId}/getKitchens', 'UserController@getKitchens');
Route::get('users/{userId}/getSubscriptions', 'UserController@getSubscriptions');
Route::get('users/{userId}/getForkedDishes', 'UserController@getForkedDishes');

// MyKitchen
Route::get('kitchens/{id}/admins', 'KitchenController@getAdmins');
Route::get('kitchens/{id}/dishes', 'KitchenController@getDishes');
Route::get('kitchens/{id}/subscribers', 'KitchenController@getSubscribers');