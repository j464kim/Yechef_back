<?php

namespace App\Models;

use App\Exceptions\YechefException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Dish extends Model
{
	use SoftDeletes;
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
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function medias()
	{
		return $this->morphMany('App\Models\Media', 'mediable');
	}

	/**
	 * Get all of the Dish's likes.
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
			'description' => 'required',
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
			Log::error('Could not find the dish with id: ' . $id);
			throw new YechefException(11500);
		}
	}
}
