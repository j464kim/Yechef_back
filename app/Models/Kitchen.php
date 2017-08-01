<?php

namespace App\Models;

use App\Traits\ModelService;
use App\Traits\Reactionable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int id
 *
 * Class Kitchen
 * @package App\Models
 */
class Kitchen extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	use Reactionable, ModelService;

	/**
	 * Enable softDeletes cascade soft-deletes related models
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Cascade soft-deletes related models
	 */
	protected $cascadeDeletes = ['dishes'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['slug', 'name', 'address', 'phone', 'email', 'description', 'lat', 'lng'];

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

	public function users()
	{
		return $this->belongsToMany('App\Models\User')->withPivot('role', 'verified')->withTimestamps();
	}

	public function orders()
	{
		return $this->hasMany('App\Models\Order');
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
	public static function getValidationRule($kitchenId = null)
	{
		$rule = array(
			'name'        => 'bail|required',
			'email'       => 'required|email|max:255|unique:kitchens,email,' . $kitchenId,
			'phone'       => 'required',
			'address'     => 'required',
			'description' => 'required',
			'lat'         => 'required|numeric',
			'lng'         => 'required|numeric'
		);

		return $rule;
	}

}
