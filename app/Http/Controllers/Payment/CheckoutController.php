<?php

namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Controller;
use App\Services\Mail\BuyerMailer;
use App\Services\Mail\SellerMailer;
use App\Services\Payment\StripeService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Payment;
use App\Models\Transaction;

class CheckoutController extends Controller
{
	private $orderCtrl;
	protected $buyerMailer, $sellerMailer, $stripe, $stripeService;

	public function __construct(
		Application $app,
		OrderController $orderCtrl,
		BuyerMailer $buyerMailer,
		SellerMailer $sellerMailer,
		Stripe $stripe,
		StripeService $stripeService
	) {
		parent::__construct($app);

		$this->orderCtrl = $orderCtrl;
		$this->buyerMailer = $buyerMailer;
		$this->sellerMailer = $sellerMailer;
		$this->stripe = $stripe;
		$this->stripeService = $stripeService;
	}

	public function charge(Request $request)
	{
		$validationRule = Transaction::getValidationRule();
		$this->validateInput($request, $validationRule);

		$buyer = $this->getUser($request);

		// If a user already has a stripe account, retrieve that. Otherwise, create one
		$customer = $this->stripeService->addOrCreateCustomer($request);

		// retrieve payment info from DB or create one
		$paymentInfo = Payment::firstOrCreate(
			[
				'user_id'   => $buyer->id,
				'stripe_id' => $customer->id,
			]
		);
		// charge customer (hold it until captured)
		$charge = $this->stripeService->chargeCustomer($request, $customer->id);

		// store it into DB
		$transaction = Transaction::create(
			[
				"payment_id" => $paymentInfo->id,
				"kitchen_id" => $request->input('kitchenId'),
				"charge_id"  => $charge->id,
				"currency"   => $charge->currency,
				"total"      => $request->input('total'),
				"buyer_fee"  => $request->input('serviceFee'),
				"seller_fee" => $request->input('serviceFee'),
			]
		);

		// create order & its items
		$order = $this->orderCtrl->store($transaction->id, $transaction->payment->user_id,
			$request->input('kitchenId'));
		$seller = $order->kitchen->getBoss();

		// delete cart
		$order->cart()->delete();

		// TODO: CC email to other owners
		// send order request email to kitchen owner
		$this->buyerMailer->sendOrderRequested($buyer, $order);
		$this->sellerMailer->sendOrderRequested($seller, $order);

		return response()->success($order, 17000);
	}
}