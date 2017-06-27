<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\YechefException;

class Cart extends Model
{

	public $totalPrice = 0;

	public function __construct()
	{
		parent::__construct();
		$this->total_price = 0;
	}

	public function items()
	{
		return $this->hasMany('App\Models\CartItem');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function add($item, $id)
	{

	}

	public function findItemByDish($id)
	{
		try {
			return $this->items()->where('dish_id', $id)->firstOrFail();
		} catch (\Exception $e) {
			throw new YechefException(18503, $e->getMessage());
		}
	}

}