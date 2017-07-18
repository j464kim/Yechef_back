<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Foundation\Application;
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
				"refunded_amount" => 0,
				"refunded"        => 0,
			)
		);

		return $transaction;
	}

}