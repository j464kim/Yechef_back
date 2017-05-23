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
	protected $fillable = ['slug', 'name', 'description'];

	/**
	 * Enable softDeletes
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get all of the Dish's comments.
	 */
	public function medias()
	{
		return $this->morphMany('App\Models\Media', 'mediable');
	}

	public static function getValidation($id = null)
	{
		Return [
			'name'        => 'bail|required',
			'description' => 'required',
		];
	}

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
