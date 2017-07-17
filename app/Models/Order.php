<?php

namespace App\Models;

use App\Exceptions\YechefException;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
	use SoftDeletes, CascadeSoftDeletes;

	protected $cascadeDeletes = ['items'];

	protected $dates = ['deleted_at'];

	protected $fillable = [
		'transaction_id',
		'user_id',
		'kitchen_id',
		'status',
	];

	public function cart()
	{
		$cart = Cart::where('user_id', $this->user_id)
			->where('kitchen_id', $this->kitchen_id)
			->firstOrFail();

		return $cart;
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function kitchen()
	{
		return $this->belongsTo('App\Models\Kitchen');
	}

	public function items()
	{
		return $this->hasMany('App\Models\OrderItem');
	}

	public function transaction()
	{
		return $this->belongsTo('App\Models\Transaction');
	}

}