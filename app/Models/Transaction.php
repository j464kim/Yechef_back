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
		'kitchen_id',
		'charge_id',
		'currency',
		'total',
		'buyer_fee',
		'seller_fee',
		'captured_amount',
		'refunded_amount',
		'released',
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
			'token'      => 'bail|required',
			'total'      => 'required',
			'currency'   => 'required',
			'kitchenId'  => 'required',
			'serviceFee' => 'required'
		);

		return $rule;
	}

	public function setTotalAttribute($value)
	{
		$this->attributes['total'] = stripe_to_db($value);
	}

	public function setBuyerFeeAttribute($value)
	{
		$this->attributes['buyer_fee'] = stripe_to_db($value);
	}

	public function setSellerFeeAttribute($value)
	{
		$this->attributes['seller_fee'] = stripe_to_db($value);
	}

	public function setCapturedAmountAttribute($value)
	{
		$this->attributes['captured_amount'] = stripe_to_db($value);
	}

	public function setRefundedAmountAttribute($value)
	{
		$this->attributes['refunded_amount'] = stripe_to_db($value);
	}

	public function getTotalAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function getBuyerFeeAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function getSellerFeeAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function getCapturedAmountAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function getRefundedAmountAttribute($value)
	{
		return db_to_stripe($value);
	}

	public function storeCapturedAmount($amountToCapture)
	{
		$this->captured_amount = $amountToCapture ?: $this->total;
		$this->save();
	}

	public function storeRefundedAmount($partialAmount = null)
	{
		if (!$partialAmount) {
			$this->released = 1;
		}
		$this->refunded_amount = $partialAmount ?: $this->total;
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
		$argument = [];
		if ($partialAmount) {
			$argument = array(
				"amount" => $partialAmount
			);
		}

		$charge->refund($argument);

		$this->storeRefundedAmount($partialAmount);
	}

}