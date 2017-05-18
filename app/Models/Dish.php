<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Yechef\DishRatingable as Ratingable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Dish extends Model
{
	use SoftDeletes;
	use Ratingable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//TODO: Add ingredient
	protected $fillable = ['slug', 'name', 'description', 'price', 'kitchen_id'];

	/**
	 * Enable softDeletes
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Many to many relationship to media
	 */
	public function media()
	{
		return $this->belongsToMany('App\Models\Media');
	}

	public function ingredients()
	{
		//TODO
//		return $this->hasMany("App\Models\Ingredient");
	}

	public function kitchen()
	{
		return $this->belongsTo('App\Models\Kitchen');
	}

	public static function getValidation($id = null)
	{
		Return [
			'name'        => 'bail|required',
			'description' => 'bail|required',
			'kitchen_id'  => 'bail|required|integer',
			'price'       => 'required|numeric',
			//TODO: ingredient
//			'ingredient_id' => 'integer',
		];
	}

	public static function findDish($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return Dish::with('media')->findOrFail($id);

			} else {
				return Dish::findOrFail($id);
			}
		} catch (ModelNotFoundException $e) {
			Log::error('Could not find the dish with id: ' . $id);
			throw new YechefException(11500);
		}
	}
}
