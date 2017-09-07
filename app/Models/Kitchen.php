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
	protected $cascadeDeletes = ['dishes', 'business_hours'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['slug', 'name', 'country', 'address', 'phone', 'email', 'description', 'lat', 'lng'];

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

	public function businessHours()
	{
		return $this->hasMany('App\Models\BusinessHour');
	}

	// TODO: Boss is the person who receives money. Position of 'boss' can be granted to others by current boss
	public function getBoss()
	{
		return $this->users()->firstOrFail();
	}

	public function getBusinessHourByDay($day)
	{
		return $this->businessHours()->where('day', $day)->firstOrFail();
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
	 * Set a unique token to be used for verifying email
	 */
	public static function boot()
	{
		// listen to any model event that will be fired
		parent::boot();

		// listen for a new record being created
		static::created(function ($kitchen) {
			// create a default business hours for a new kitchen
			for ($day=1; $day<=7; $day++) {
				$kitchen->businessHours()->save(
					new BusinessHour(
						[
							'active' => 1,
							'day' => $day,
							'open_time' => '09_00',
							'close_time' => '17_00'
						]
					)
				);
			}

		});
	}

	public function addRatingAttributes()
	{
		$dishes = $this->dishes()->get();

		$this['totalTasteRating'] = 0;
		$this['totalVisualRating'] = 0;
		$this['totalQuantityRating'] = 0;
		$this['totalRating'] = 0;
		$dishesWithoutRating = 0;
		$this['ratingsCount'] = 0;
		$this['totalDishes'] = 0;

		if (sizeof($dishes) > 0) {
			foreach ($dishes as $dish) {
				$avgRatings = $dish->avgRating();
				if ($avgRatings['taste_rating'] < 0 && $avgRatings['visual_rating'] < 0 && $avgRatings['quantity_rating_rating'] < 0) {
					$dishesWithoutRating++;
				} else {
					$this['totalTasteRating'] += $avgRatings['taste_rating'];
					$this['totalVisualRating'] += $avgRatings['visual_rating'];
					$this['totalQuantityRating'] += $avgRatings['quantity_rating'];
					$this['ratingsCount'] += $dish->countRatings();
					$this['totalDishes'] = $this['totalDishes'] + 1;
				}
			}
			$dishesWithRating = sizeof($dishes) - $dishesWithoutRating;
			$this['totalTasteRating'] /= sizeof($dishesWithRating);
			$this['totalVisualRating'] /= sizeof($dishesWithRating);
			$this['totalQuantityRating'] /= sizeof($dishesWithRating);
			$this['totalRating'] = ($this['totalTasteRating'] + $this['totalVisualRating'] + $this['totalQuantityRating']) / sizeof($avgRatings);
		}

	}

	/**
	 * @return array
	 */
	public static function getValidationRule($kitchenId = null)
	{
		$rule = array(
			'country'     => 'bail|required',
			'address'     => 'required',
			'name'        => 'required',
			'email'       => 'required|email|max:255|unique:kitchens,email,' . $kitchenId,
			'phone'       => 'required',
			'description' => 'required',
			'lat'         => 'required|numeric',
			'lng'         => 'required|numeric'
		);

		return $rule;
	}

}
