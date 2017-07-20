<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\StripeService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Stripe;

class PaymentController extends Controller
{
	protected $stripe, $customer, $secretKey, $stripeService;

	public function __construct(
		Application $app,
		Customer $customer,
		StripeService $stripeService
	) {
		parent::__construct($app);

		$this->customer = $customer;
		$this->stripeService = $stripeService;
	}

	public function index(Request $request)
	{
		$user = $this->getUser($request);
		$customer = null;

		if ($paymentAccount = $user->payment) {
			$customer = $this->customer->retrieve($paymentAccount->stripe_id);
		}

		return response()->success($customer);
	}

	public function store(Request $request)
	{
		$user = $this->getUser($request);
		$customer = $this->stripeService->addCard($request);

		// retrieve payment info from DB or create one
		$paymentInfo = Payment::firstOrCreate(
			[
				'user_id'   => $user->id,
				'stripe_id' => $customer->id,
			]
		);

		return response()->success($paymentInfo);

	}

	public function destroy(Request $request, $cardId)
	{
		$this->stripeService->removeCard($request, $cardId);
	}

}