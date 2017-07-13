<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Stripe\Charge;

class TransactionController extends Controller
{

	public function store($request, $paymentId, $chargeId)
	{
		$transaction = Transaction::create(
			array(
				"payment_id"      => $paymentId,
				"charge_id"       => $chargeId,
				"currency"        => $request->input('currency'),
				"amount"          => $request->input('amount'),
				"captured_amount" => 0,
				"captured"        => 0,
			)
		);

		return $transaction;
	}

	public function captureAmount(Charge $charge, $amountToCapture)
	{
		// capture amount
		$charge->capture([
			"amount" => $amountToCapture
		]);

		$transaction = Transaction::getTransactionByChargeId($charge->id);

		$transaction->storeCapturedAmount($amountToCapture);
	}

}