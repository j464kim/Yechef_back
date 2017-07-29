<?php

namespace App\Http\Controllers\Payment;


use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Services\AppMailer;
use App\Services\Payment\StripeService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Kitchen;

class CheckoutController extends Controller
{
	private $orderCtrl;
	protected $mailer, $stripe, $stripeService;

	public function __construct(
		Application $app,
		OrderController $orderCtrl,
		AppMailer $mailer,
		Stripe $stripe,
		StripeService $stripeService
	) {
		parent::__construct($app);

		$this->orderCtrl = $orderCtrl;
		$this->mailer = $mailer;
		$this->stripe = $stripe;
		$this->stripeService = $stripeService;
	}

	public function charge(Request $request)
	{
		$validationRule = Transaction::getValidationRule();
		$this->validateInput($request, $validationRule);

		$user = $this->getUser($request);

		// If a user already has a stripe account, retrieve that. Otherwise, create one
		$customer = $this->stripeService->addOrCreateCustomer($request);

		// retrieve payment info from DB or create one
		$paymentInfo = Payment::firstOrCreate(
			[
				'user_id'   => $user->id,
				'stripe_id' => $customer->id,
			]
		);

		// charge customer (hold it until captured)
		$charge = $this->stripeService->chargeCustomer($request, $customer->id);

		// store it into DB
		$transaction = Transaction::create(
			[
				"payment_id"  => $paymentInfo->id,
				"charge_id"   => $charge->id,
				"currency"    => $request->input('currency'),
				"amount"      => $request->input('amount'),
				"service_fee" => $request->input('serviceFee'),
			]
		);

		// create order & its items
		$order = $this->orderCtrl->store($transaction->id, $transaction->payment->user_id,
			$request->input('kitchenId'));

		// delete cart
		$order->cart()->delete();

		// TODO: CC email to other owners
		// send order request email to kitchen owner
		$owner = $order->kitchen->users->first();
		$this->mailer->sendOrderRequest($owner, $order);

		return response()->success($order, 17000);
	}
}