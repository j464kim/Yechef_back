<?php

namespace App\Models;

use App\Exceptions\YechefException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Reactionable;
use Illuminate\Support\Facades\Log;

/**
 *
 * @property int id
 *
 * Class Kitchen
 * @package App\Models
 */
class Kitchen extends Model
{
	use SoftDeletes;
	use Reactionable;
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'address', 'phone', 'email', 'description', 'user_id'];

	/**
	 * Get all of the Kitchen's medias.
	 */
	public function medias()
	{
		return $this->morphMany('App\Models\Media', 'mediable');
	}

	public function dishes()
	{
		return $this->hasMany('App\Models\Dish');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
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
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'name'        => 'bail|required',
			'email'       => 'required',
			'phone'       => 'required',
			'address'     => 'required',
			'description' => 'required',
		);

		return $rule;
	}

	/**
	 * @param $id
	 * @param bool $withMedia
	 * @return \Illuminate\Database\Eloquent\Collection|Model
	 * @throws YechefException
	 */
	public static function findKitchen($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return Kitchen::with('medias')->findOrFail($id);
			} else {
				return Kitchen::findOrFail($id);
			}
		} catch (\Exception $e) {
			throw new YechefException(12501);
		}
	}

}
