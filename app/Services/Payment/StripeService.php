<?php

namespace App\Services\Payment;

use App\Exceptions\YechefException;
use App\Models\Kitchen;
use App\Models\PayoutAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\Balance;
use Stripe\Charge;
use Stripe\Customer;
use App\Http\Controllers\Controller;
use Stripe\FileUpload;
use Stripe\Stripe;

class StripeService
{
	private $controller;
	protected $customer, $charge, $stripe, $account, $balance, $payoutAccount, $fileUpload;

	function __construct(
		Customer $customer,
		Controller $controller,
		Charge $charge,
		Stripe $stripe,
		Account $account,
		Balance $balance,
		FileUpload $fileUpload
	) {
		$this->customer = $customer;
		$this->controller = $controller;
		$this->charge = $charge;
		$this->stripe = $stripe;
		$this->account = $account;
		$this->balance = $balance;
		$this->fileUpload = $fileUpload;

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

	public function getConnect(Request $request)
	{
		$user = $this->controller->getUser($request);
		try {
			$connectId = $payoutAccount = $user->payoutAccount->connect_id;
		} catch (\Exception $e) {
			throw new YechefException(21500, $e->getMessage());
		}

		$connect = $this->account->retrieve($connectId);

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
			// If a user already has a stripe account
			$customer = $this->customer->retrieve($paymentAccount->stripe_id);

			// if user is trying to check out with existing payment method
			if (!$request->input('token')) {
				return $customer;
			}

			// add a card to the account if user entered credit card info on checkout
			$cardFingerprints = [];
			$cards = $customer->sources->data;
			foreach ($cards as $index => $card) {
				array_push($cardFingerprints, $card->fingerprint);
			}

			$newCard = $customer->sources->create(
				[
					'source' => $request->input('token'),
				]
			);

			// if the entered card already exists in customer account, remove it
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

	public function updatePayoutAddress(Request $request)
	{
		$connect = $this->getConnect($request);

		$connect->legal_entity->address->state = $request->input('state');
		$connect->legal_entity->address->city = $request->input('city');
		$connect->legal_entity->address->line1 = $request->input('line1');
		$connect->legal_entity->address->line2 = $request->input('line2');
		$connect->legal_entity->address->postal_code = $request->input('postal_code');

		$connect->save();
	}

	public function updatePayoutUserInfo(Request $request)
	{
		$connect = $this->getConnect($request);

		$connect->legal_entity->type = PayoutAccount::TYPE;
		$connect->legal_entity->dob->day = $request->input('dob_day');
		$connect->legal_entity->dob->month = $request->input('dob_month');
		$connect->legal_entity->dob->year = $request->input('dob_year');
		$connect->legal_entity->first_name = $request->input('first_name');
		$connect->legal_entity->last_name = $request->input('last_name');

		$connect->save();
	}

	public function updateCard(Request $request, $cardId)
	{
		$card = $this->getCardById($request, $cardId);

		$card->address_city = $request->input('address_city');
		$card->address_line1 = $request->input('address_line1');
		$card->address_zip = $request->input('address_zip');

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
//		Take identical Service Fee from both buyers & sellers for now
		$totalCharged = $request->input('total');
		$amountToSeller = $request->input('total') - $request->input('serviceFee') - $request->input('serviceFee');
		$kitchen = Kitchen::findById($request->input('kitchenId'));
		$boss = $kitchen->getBoss();
		try {
			$charge = $this->charge->create(
				[
					"amount"      => $totalCharged,
					"currency"    => get_currency($kitchen->country),
					"customer"    => $customerId,
					"capture"     => false,
					"description" => "YeChef - Charged $totalCharged for purchasing dishes from $kitchen->name",
					"destination" => [
						"amount"  => $amountToSeller,
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

	public function addExternalAccount(Request $request)
	{
		$connect = $this->getConnect($request);

		$bankFingerprints = [];
		$banks = $connect->external_accounts->data;
		foreach ($banks as $index => $bank) {
			array_push($bankFingerprints, $bank->fingerprint);
		}

		$newBank = $connect->external_accounts->create(
			[
				"external_account" => $request->token,
			]
		);

		Log::info($newBank);

		if (in_array($newBank->fingerprint, $bankFingerprints)) {
			$connect->external_accounts->retrieve($newBank->id)->delete();
			Log::info('the same account is not being added twice');
		}
	}

	public function deleteExternalAccount(Request $request, $id)
	{
		$connect = $this->getConnect($request);

		$externalAccount = $connect->external_accounts->retrieve($id);
		$externalAccount->delete();
	}

	public function switchDefaultExternalAccount(Request $request)
	{
		$connect = $this->getConnect($request);

		$externalAccount = $connect->external_accounts->retrieve($request->input('id'));
		$externalAccount->default_for_currency = true;
		$externalAccount->save();
	}

	public function uploadFile(Request $request, $filePath)
	{
		$connect = $this->getConnect($request);

		$fileStripe = $this->fileUpload->create(
			array(
				"purpose" => "identity_document",
				"file"    => fopen($filePath, "r")
			),
			array("stripe_account" => $connect->id)
		);
		Log::info($fileStripe);
		$connect->legal_entity->verification->document = $fileStripe->id;
		$connect->save();
	}
}