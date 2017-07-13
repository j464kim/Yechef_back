<?php

namespace App\Http\Controllers;


use App\Exceptions\YechefException;
use App\Http\Controllers\Auth\TransactionController;
use App\Http\Controllers\Auth\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Models\Payment;
use App\Models\Transaction;

class CheckoutController extends Controller
{
	private $paymentCtrl, $transactionCtrl;

	public function __construct(
		Application $app,
		PaymentController $paymentCtrl,
		TransactionController $transactionCtrl
	) {
		parent::__construct($app);

		$this->paymentCtrl = $paymentCtrl;
		$this->transactionCtrl = $transactionCtrl;
	}

	public function charge(Request $request)
	{
		$validationRule = Transaction::getValidationRule();
		$this->validateInput($request, $validationRule);

		$user = $this->getUser($request);

		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		$secretKey = config('services.stripe.secret_key');
		Stripe::setApiKey($secretKey);

		// Token is created using Stripe.js or Checkout!
		// Get the payment token submitted by the form:
		$token = $request->input('token');

		// If a user already has a stripe account, retrieve that
		if ($paymentAccount = $user->getPaymentAccount()) {
			$customer = Customer::retrieve($paymentAccount->stripe_id);
		} else {
			// Otherwise, create one
			try {
				$customer = Customer::create(array(
					"email"  => $user->email,
					"source" => $token,
				));
			} catch (\Exception $e) {
				throw new YechefException(17501, $e->getMessage());
			}

			// store it into DB
			$paymentAccount = $this->paymentCtrl->store($user->id, $customer->id);
		}

		// charge customer (hold it until captured)
		try {
			$charge = Charge::create(array(
				"amount"      => $request->input('amount'),
				"currency"    => $request->input('currency'),
				"customer"    => $customer->id,
				"capture"     => false,
				"description" => "Example charge",
			));
		} catch (\Exception $e) {
			throw new YechefException(17502, $e->getMessage());
		}

		// store it into DB
		$transaction = $this->transactionCtrl->store($request, $paymentAccount->id, $charge->id);

		// TODO: hardcoded to half the amount for now
		$amountToCapture = round($transaction->amount / 2);
		$this->transactionCtrl->captureAmount($charge, $amountToCapture);

		return response()->success(17000);
	}
}