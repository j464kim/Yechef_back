<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Reserved Return Codes
    |--------------------------------------------------------------------------
    |
    | Range 0 <= x < 1000
    |
    */
	'0'     => 'Invalid error code',
	'1'     => 'Request Success',
	'2'     => 'Unautherized resource',

	/*
	|--------------------------------------------------------------------------
	| Internal return codes
	|--------------------------------------------------------------------------
	|
	| Range 1000 <= x < 10000
	|
	*/
	'1000'  => 'Oauth proxy error',

	/*
    |--------------------------------------------------------------------------
    | Public facing return codes
    |--------------------------------------------------------------------------
    |
    | Range 10000 <= x
    |
    */

	/**
	 * Authentication related
	 * Success Range 10000 <= x < 10500
	 * Error Range 10500 <= x < 11000
	 */
	// success
	'10000' => 'Access token granted',
	'10001' => 'Access token refreshed',
	'10002' => 'Logout success',
	// fail
	'10500' => 'Please provide your email and password',
	'10501' => 'Your email and password are wrong',
	'10502' => 'Refresh token required',
	'10503' => 'Fail to refresh access token',
	'10504' => 'No user session found',

	/**
	 * Dish related
	 * Success Range 11000 <= x < 11500
	 * Error Range 11500 <= x < 12000
	 */
	// success
	'11000' => 'Found the dish',
	'11001' => 'Dish successfully created',
	'11002' => 'Dish successfully updated',
	'11003' => 'Dish successfully deleted',
	'11004' => 'Dish successfully rated',
	'11005' => 'Dish rating successfully updated',
	'11006' => 'Dish rating successfully deleted',
	// fail
	'11500' => 'Unable to find the dish',
	'11501' => 'Invalid request inputs for dish',
	'11502' => 'Invalid request inputs for dish rating',
	'11503' => 'Unable to find the dish rating',

	/**
	 * Kitchen related
	 * Success Range 12000 <= x < 12500
	 * Error Range 12500 <= x < 13000
	 */
	// success
	'12000' => 'Kitchen is successfully created',
	'12001' => 'Kitchen is successfully updated',
	'12002' => 'Kitchen is successfully deleted',
	// fail
	'12500' => 'Please make sure all fields filled out',
	'12501' => 'Kitchen could not be found',
];