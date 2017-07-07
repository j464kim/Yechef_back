<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Traits\Reactionable;
use App\Traits\ModelService;
use App\Yechef\DishRatingable as Ratingable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

class Dish extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	use Searchable;
	use Ratingable, Reactionable, ModelService;

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
	public static function getValidationRule($id = null)
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

}
