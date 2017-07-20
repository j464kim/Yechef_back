<?php

namespace App\Services\Payment;

use App\Exceptions\YechefException;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Customer;
use App\Http\Controllers\Controller;
use Stripe\Stripe;

class StripeService
{
	private $controller;
	protected $customer, $charge, $stripe;

	function __construct(Customer $customer, Controller $controller, Charge $charge, Stripe $stripe)
	{
		$this->customer = $customer;
		$this->controller = $controller;
		$this->charge = $charge;
		$this->stripe = $stripe;

		$secretKey = config('services.stripe.secret_key');
		$this->stripe->setApiKey($secretKey);
	}

	public function addCard(Request $request)
	{
		$user = $this->controller->getUser($request);

		if ($paymentAccount = $user->payment) {
			// If a user already has a stripe account, add a card to the account
			$customer = $this->customer->retrieve($paymentAccount->stripe_id);
			$cardFingerprints = [];
			$cards = $customer->sources->data;
			foreach ($cards as $index => $card) {
				array_push($cardFingerprints, $card->fingerprint);
			}

			$newCard = $customer->sources->create(
				[
					'source' => $request->input('token')
				]
			);
			Log::info('new card was added to customer');
			Log::info($newCard);

			if (in_array($newCard->fingerprint, $cardFingerprints)) {
				Log::info('same card was added, so it is being deleted');
				$customer->sources->retrieve($newCard->id)->delete();
			}

		} else {
			// Otherwise, create one
			try {
				Log::info($request->input('token'));
				$customer = $this->customer->create(
					[
						'email'  => $user->email,
						'source' => $request->input('token'),
					]
				);
			} catch (\Exception $e) {
				throw new YechefException(17501, $e->getMessage());
			}
		}

		return $customer;
	}

	public function removeCard(Request $request, $cardId)
	{
		$user = $this->controller->getUser($request);
		$paymentAccount = $user->payment;

		$customer = $this->customer->retrieve($paymentAccount->stripe_id);
		$customer->sources->retrieve($cardId)->delete();
	}

	public function chargeCustomer(Request $request, $customerId)
	{
		try {
			$charge = $this->charge->create(
				[
					"amount"      => $request->input('amount'),
					"currency"    => $request->input('currency'),
					"customer"    => $customerId,
					"capture"     => false,
					"description" => "Example charge",
				]
			);
		} catch (\Exception $e) {
			throw new YechefException(17502, $e->getMessage());
		}

		return $charge;
	}

}