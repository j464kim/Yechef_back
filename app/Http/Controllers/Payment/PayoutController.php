<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\StripeService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Payout;
use App\Models\PayoutAccount;
use Stripe\Stripe;

class PayoutController extends Controller
{
	protected $stripe, $customer, $secretKey, $stripeService, $payment;

	public function __construct(
		Application $app,
		Customer $customer,
		StripeService $stripeService,
		Payout $payout
	) {
		parent::__construct($app);

		$this->customer = $customer;
		$this->stripeService = $stripeService;
		$this->payout = $payout;
	}

	public function index(Request $request)
	{
		$connect = $this->stripeService->getOrCreateConnect($request);
		$balance = $this->stripeService->getBalance($connect->id);
		$payoutAccount = PayoutAccount::firstOrCreate(
			['connect_id' => $connect->id]
		);
		Log::info($balance);
		$payoutInfo = (object)array(
			'id'               => $payoutAccount->id,
			'country'          => $connect->country,
			'default_currency' => $connect->default_currency,
			'email'            => $connect->email,
			'external_account' => $connect->external_accounts->has_more,
			'balance'          => $balance->available[0]->amount
		);

		return response()->success($payoutInfo);
	}

	public function store(Request $request)
	{
		$user = $this->getUser($request);
		$connect = $this->stripeService->getOrCreateConnect($request);

		// Profit from kitchen generally goes to the owner who created the kitchen at first
		// Store the connect account into DB
		$payoutAccount = PayoutAccount::firstOrCreate(
			[
				'user_id'    => $user->id,
				'connect_id' => $connect->id,
				'country'    => $connect->country,
			]
		);

		return response()->success($payoutAccount);
	}

	public function update(Request $request, $id)
	{
		$validationRule = PayoutAccount::getValidationRule();
		$this->validate($request, $validationRule);

		$connect = $this->stripeService->getOrCreateConnect($request);

		$connect->legal_entity->address->state = $request->input('state');
		$connect->legal_entity->address->city = $request->input('city');
		$connect->legal_entity->address->line1 = $request->input('line1');
		$connect->legal_entity->address->line2 = $request->input('line2');
		$connect->legal_entity->address->postal_code = $request->input('postal_code');
		$connect->save();

		Log::info($connect);
	}
}