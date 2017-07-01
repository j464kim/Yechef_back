<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Traits\Reactionable;
use App\Yechef\DishRatingable as Ratingable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	use Ratingable, Reactionable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//TODO: Add ingredient
	protected $fillable = [
		'slug',
		'name',
		'nationality',
		'description',
		'price',
		'gluten_free',
		'vegetarian',
		'vegan',
		'kitchen_id'
	];

	/**
	 * Enable softDeletes & cascade soft-deletes
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Cascade soft-deletes related models
	 */
	protected $cascadeDeletes = ['ratings'];

	/**
	 * Get all of the Dish's comments.
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function medias()
	{
		return $this->morphMany('App\Models\Media', 'mediable');
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

	public function ratings()
	{
		return $this->hasMany('App\Models\DishRating');
	}

	/**
	 * Get all of the Dish's reactions.
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function reactions()
	{
		return $this->morphMany('App\Models\Reaction', 'reactionable');
	}

	/**
	 * @param null $id
	 * @return array
	 */
	public static function getValidation($id = null)
	{
		Return [
			'name'        => 'bail|required',
			'description' => 'bail|required',
			'kitchen_id'  => 'bail|required|integer',
			'price'       => 'bail|required|numeric',
			'nationality' => 'required'
			//TODO: ingredient
//			'ingredient_id' => 'integer',
		];
	}

	/**
	 * @param $id
	 * @param bool $withMedia
	 * @return \Illuminate\Database\Eloquent\Collection|Model
	 * @throws YechefException
	 */
	public static function findDish($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return Dish::with('medias')->findOrFail($id);

			} else {
				return Dish::findOrFail($id);
			}
		} catch (ModelNotFoundException $e) {
			throw new YechefException(11500);
		}
	}

}
