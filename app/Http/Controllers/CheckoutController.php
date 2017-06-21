<?php

namespace App\Http\Controllers;


use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class CheckoutController
{
	public function charge(Request $request)
	{
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		$scretKey = config('services.stripe.secret_key');
		Stripe::setApiKey($scretKey);

		// Token is created using Stripe.js or Checkout!
		// Get the payment token submitted by the form:
		$token = $request->input('token');

		try {
			// Create a Customer:
			$customer = Customer::create(array(
				"email"  => $request->input('email'),
				"source" => $token,
			));
		} catch (\Exception $e) {
			throw new YechefException(17501, $e->getMessage());
		}

		try {
			// Charge the Customer instead of the card:
			$charge = Charge::create(array(
				"amount"      => $request->input('amount'),
				"currency"    => $request->input('currency'),
				"customer"    => $customer->id,
				"description" => "Example charge",
			));
		} catch (\Exception $e) {
			throw new YechefException(17502, $e->getMessage());
		}

		// TODO: Save the customer ID and other info in a database for later.
		// ...
	}
}