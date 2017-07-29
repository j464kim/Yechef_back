<?php

namespace App\Services\Payment;

use App\Exceptions\YechefException;
use App\Models\Kitchen;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\Balance;
use Stripe\Charge;
use Stripe\Customer;
use App\Http\Controllers\Controller;
use Stripe\Stripe;

class StripeService
{
	private $controller;
	protected $customer, $charge, $stripe, $account, $balance;

	function __construct(
		Customer $customer,
		Controller $controller,
		Charge $charge,
		Stripe $stripe,
		Account $account,
		Balance $balance
	) {
		$this->customer = $customer;
		$this->controller = $controller;
		$this->charge = $charge;
		$this->stripe = $stripe;
		$this->account = $account;
		$this->balance = $balance;

		$secretKey = config('services.stripe.secret_key');
		$this->stripe->setApiKey($secretKey);
	}

	public function showCard(Request $request, $index)
	{
		$user = $this->controller->getUser($request);
		$paymentAccount = $user->payment;

		$customer = $this->customer->retrieve($paymentAccount->stripe_id);
		$card = $customer->sources->data[$index];

		return $card;
	}

	public function getOrCreateConnect(Request $request)
	{
		$user = $this->controller->getUser($request);
		$connect = null;

		// If user already has a payout method, retrieve that
		if ($payoutAccount = $user->payoutAccount) {

			$connect = $this->account->retrieve($payoutAccount->connect_id);

		} elseif ($country = $request->input('country')) {

			// Otherwise, create one
			$connect = $this->account->create(
				[
					"country" => $country,
					"type"    => "custom",
					"email"   => $user->email,
				]
			);
		}
		return $connect;
	}

	public function getBalance($connectId)
	{
		return $this->balance->retrieve([
			'stripe_account' => $connectId
		]);
	}

	public function addOrCreateCustomer(Request $request)
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

			if (in_array($newCard->fingerprint, $cardFingerprints)) {
				$customer->sources->retrieve($newCard->id)->delete();
			}

		} else {
			// Otherwise, create one
			try {
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

	public function updateCard(Request $request, $cardId)
	{
		$card = $this->getCardById($request, $cardId);

		$card->name = $request->input('name');
		$card->exp_month = $request->input('exp_month');
		$card->exp_year = $request->input('exp_year');
		$card->save();

		return $card;
	}

	public function removeCard(Request $request, $cardId)
	{
		$card = $this->getCardById($request, $cardId);

		$card->delete();

		return $card;
	}

	public function chargeCustomer(Request $request, $customerId)
	{
		$totalCharge = $request->input('amount') + $request->input('serviceFee');
		$kitchen = Kitchen::findById($request->input('kitchenId'));
		$boss = $kitchen->getBoss();

		try {
			$charge = $this->charge->create(
				[
					"amount"      => $totalCharge,
					"currency"    => $request->input('currency'),
					"customer"    => $customerId,
					"capture"     => false,
					"description" => "Example charge",
					"destination" => [
						"amount"  => $request->input('amount'),
						"account" => $boss->payoutAccount->connect_id,
					],
				]
			);
		} catch (\Exception $e) {
			throw new YechefException(17502, $e->getMessage());
		}

		Log::info($charge);
		return $charge;
	}

	public function getCardById(Request $request, $cardId)
	{
		$user = $this->controller->getUser($request);
		$paymentAccount = $user->payment;

		$customer = $this->customer->retrieve($paymentAccount->stripe_id);
		$card = $customer->sources->retrieve($cardId);

		return $card;
	}

}