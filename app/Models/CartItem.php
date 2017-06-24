<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['cart_id', 'dish_id', 'quantity', 'price'];

	public function cart()
	{
		return $this->belongsTo('App\Models\Cart');
	}

	public function dish()
	{
		return $this->belongsTo('App\Models\Dish');
	}

	public static function getValidationRule()
	{
		$rule = array(
			'cart_id'  => 'bail|required',
			'dish_id'  => 'bail|required',
			'quantity' => 'bail|required',
			'price'    => 'bail|required',
		);

		return $rule;
	}
}