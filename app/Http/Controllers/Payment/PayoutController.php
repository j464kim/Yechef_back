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

	public function store(Request $request)
	{
		$user = $this->getUser($request);
		$connect = $this->stripeService->getOrCreateConnect($request);

		// Profit from kitchen generally goes to the perosn who created the kitchen at first
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
}
