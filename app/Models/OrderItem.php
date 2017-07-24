<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'order_id',
		'dish_id',
		'quantity',
		'captured_quantity',
	];

	public function order()
	{
		return $this->belongsTo('App\Models\Order');
	}

	public function dish()
	{
		return $this->belongsTo('App\Models\Dish');
	}

}