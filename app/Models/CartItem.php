<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
	use SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['cart_id', 'dish_id', 'quantity'];

	public function cart()
	{
		return $this->belongsTo('App\Models\Cart');
	}

	public function dish()
	{
		return $this->belongsTo('App\Models\Dish');
	}

	public static function getValidationRule($isUpdate)
	{
		$rule = array(
			'quantity' => 'bail|required',
		);

		if (!$isUpdate) {
			$rule['dish_id'] = 'bail|required';
		}

		return $rule;
	}
}