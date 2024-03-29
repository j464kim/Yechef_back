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
	'2'     => 'Please login before accessing the requested resource',
	'3'     => 'You are not allowed to access the requested resource',

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
	'10003' => 'Password is changed successfully',
	'10004' => 'Success! Please check your email for confirmation link!',
	'10005' => 'Verified your email successfully! You can now log in!',
	// fail
	'10500' => "Your account email has not yet been verified. Please check your email for confirmation link",
	'10501' => 'Your email and password are wrong',
	'10502' => 'Refresh token required',
	'10503' => 'Fail to refresh access token',
	'10504' => 'No user session found',
	'10506' => 'Current Password is wrong',
	'10507' => 'Could not find user that match with the token',
	'10508' => 'Could not find user that match with the email',

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
	'11501' => 'Unable to find the dish rating',
	'11502' => 'City is required for Dish Search',
	'11503' => 'User has no access to rate the dish',
	'11504' => 'Dish cannot be rated 24 hours after the order',
	'11505' => 'You have already rated the dish',

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
	'12500' => 'Cannot add/remove self as kitchen admin',
	'12501' => 'Kitchen could not be found',
	'12502' => 'This admin already exists',
	'12503' => 'Cannot find the kitchen admin',
	'12504' => 'Cannot add/remove self as kitchen admin',
	'12505' => 'You did not approve to be a kitchen owner yet',
	'12506' => 'You are not owning the kitchen',

	/**
	 * Media related
	 * Success Range 13000 <= x < 13500
	 * Error Range 13500 <= x < 14000
	 */
	// success
	'13000' => 'Successfully uploaded the media',
	'13001' => 'Successfully deleted the media',

	// fail
	'13500' => 'File does not exist',
	'13501' => 'File is not valid',

	/**
	 * Relation related
	 * Success Range 14000 <= x < 14500
	 * Error Range 14500 <= x < 15000
	 */
	// success
	'14000' => 'Successfully added reaction to the post',
	'14001' => 'Successfully deleted reaction to the post',
	'14002' => 'Successfully retrieved the number of reactions',

	// fail
	'14502' => 'User has more than 1 reactions to the post',
	'14503' => 'Could not find the reactionable',


	/**
	 * User related
	 * Success Range 15000 <= x < 15500
	 * Error Range 15500 <= x < 16000
	 */
	// success
	'15000' => 'Found the User',
	'15001' => 'Successfully updated User information',

	// fail
	'15502' => 'Failed to retrieve user information from request',
	'15503' => 'The User has set this information private',


	/**
	 * Payment related
	 * Success Range 17000 <= x < 17500
	 * Error Range 17500 <= x < 18000
	 */
	// success
	'17000' => 'Payment was authorized successfully. 
	The amount will not be charged until you receive the dish',
	'17001' => 'The card has been removed successfully',
	'17002' => 'The card has been updated successfully',

	// fail
	'17501' => 'Error occurred while creating a Stripe account',
	'17502' => 'Payment charge failed',
	'17503' => 'Unable to find transaction of the charge_id',


	/**
	 * Cart related
	 * Success Range 18000 <= x < 18500
	 * Error Range 18500 <= x < 19000
	 */
	// success
	'18000' => 'The item is added to cart',
	'18001' => 'Cart item quantity is udpated',
	'18002' => 'Item is removed from the cart',
	'18003' => 'Your cart is empty',

	// fail
	'18501' => 'Wrong inputs for cart items',
	'18502' => 'Failed to get the cart of the user',
	'18503' => 'The cart item cannot be found',

	/**
	 * Common Model related
	 * Success Range 19000 <= x < 19500
	 * Error Range 19500 <= x < 20000
	 */
	// success

	// fail
	'19500' => 'Could not find a model by the given id',

	/**
	 * Order related
	 * Success Range 20000 <= x < 20500
	 * Error Range 20500 <= x < 21000
	 */
	// success

	// fail
	'20501' => 'You are not authorized to make action for this order',
	'20502' => 'An order must be of pending status to be cancellable',

	/**
	 * Payout related
	 * Success Range 21000 <= x < 21500
	 * Error Range 21500 <= x < 22000
	 */
	// success

	// fail
	'21500' => 'Unable to get payout account of user',

];