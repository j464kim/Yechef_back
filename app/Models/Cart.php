<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\YechefException;

class Cart extends Model
{
	protected $cascadeDeletes = ['items'];

	protected $fillable = ['kitchen_id'];

	public function items()
	{
		return $this->hasMany('App\Models\CartItem');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
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