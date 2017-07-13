<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{

	public function store($userId, $stripeId)
	{
		$paymentAccount = Payment::create(
			array(
				"user_id"   => $userId,
				"stripe_id" => $stripeId,
			)
		);

		return $paymentAccount;
	}

}