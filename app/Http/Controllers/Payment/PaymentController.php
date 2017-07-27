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
	protected $stripe, $customer, $secretKey, $stripeService, $payment;

	public function __construct(
		Application $app,
		Customer $customer,
		StripeService $stripeService,
		Payment $payment
	) {
		parent::__construct($app);

		$this->customer = $customer;
		$this->stripeService = $stripeService;
		$this->payment = $payment;
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

	public function show(Request $request, $index)
	{
		$card = $this->stripeService->showCard($request, $index);

		return response()->success($card);
	}

	public function store(Request $request)
	{
		$user = $this->getUser($request);
		$validationRule = ['token' => 'required'];
		$this->validateInput($request, $validationRule);

		$customer = $this->stripeService->addOrCreateCustomer($request);

		// retrieve payment info from DB or create one
		$paymentInfo = Payment::firstOrCreate(
			[
				'user_id'   => $user->id,
				'stripe_id' => $customer->id,
			]
		);

		return response()->success($paymentInfo);
	}

	public function update(Request $request, $cardId)
	{
		// validate input for update
		$validationRule = $this->payment->getValidationRule();
		$this->validateInput($request, $validationRule);

		$updatedCard = $this->stripeService->updateCard($request, $cardId);

		return response()->success($updatedCard, 17002);
	}

	public function destroy(Request $request, $cardId)
	{
		$removedCard = $this->stripeService->removeCard($request, $cardId);
		return response()->success($removedCard, 17001);
	}

}