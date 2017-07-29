<?php

namespace App\Models;

use App\Exceptions\YechefException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stripe\Charge;

class Transaction extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'payment_id',
		'charge_id',
		'currency',
		'amount',
		'service_fee',
		'captured_amount',
		'captured',
		'refunded_amount',
		'refunded',
	];

	public function payment()
	{
		return $this->belongsTo('App\Models\Payment');
	}

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'token'     => 'bail|required',
			'amount'    => 'required',
			'currency'  => 'required',
			'kitchenId' => 'required',
		);

		return $rule;
	}

	public function setAmountAttribute($value)
	{
		$this->attributes['amount'] = stripe_to_db($value);
	}

	public function setServiceFeeAttribute($value)
	{
		$this->attributes['service_fee'] = stripe_to_db($value);
	}

	public function getAmountAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function getServiceFeeAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function storeCapturedAmount($amountToCapture)
	{
		$this->captured = 1;
		$this->captured_amount = $amountToCapture ?: $this->amount;
		$this->save();
	}

	public function storeRefundedAmount($amountToRefund)
	{
		$this->refunded = 1;
		$this->refunded_amount = $amountToRefund ?: $this->amount;
		$this->save();
	}

	public static function getTransactionByChargeId($chargeId)
	{
		try {
			// charge_id is unique
			$transaction = self::whereChargeId($chargeId)->firstOrFail();
		} catch (\Exception $e) {
			throw new YechefException(17503, $e->getMessage());
		}

		return $transaction;
	}

	public function captureAmount(Charge $charge, $partialAmount = null)
	{
		// convert decimal amount to its integer of times 100 for stripe format
		$amountToCapture = $partialAmount ? round($partialAmount) : null;

		$argument = [];
		if ($amountToCapture) {
			$argument = array(
				"amount" => $amountToCapture
			);
		}

		$charge->capture($argument);

		$this->storeCapturedAmount($amountToCapture);
	}

	public function refundAmount(Charge $charge, $partialAmount = null)
	{
		// convert decimal amount to its integer of times 100 for stripe format
		$amountToRefund = $partialAmount ? round($partialAmount) : null;

		$argument = [];
		if ($amountToRefund) {
			$argument = array(
				"amount" => $amountToRefund
			);
		}

		$charge->refund($argument);

		$this->storeRefundedAmount($amountToRefund);
	}

}