<?php

namespace App\Models;

use App\Exceptions\YechefException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'payment_id',
		'charge_id',
		'currency',
		'amount',
		'captured_amount',
		'captured',
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

	public function storeCapturedAmount($amountToCapture)
	{
		$this->captured = 1;
		$this->captured_amount = $amountToCapture;
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
}