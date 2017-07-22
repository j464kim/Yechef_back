<?php

namespace App\Models;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\YechefException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
	use SoftDeletes, CascadeSoftDeletes;

	protected $cascadeDeletes = ['items'];

	protected $dates = ['deleted_at'];

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