<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Traits\ModelService;
use App\Traits\Reactionable;
use Ghanem\Rating\Models\Rating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DishRating
 * @package App\Models
 */
class DishRating extends Rating
{
	use SoftDeletes;
	use Reactionable;
	use ModelService;

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * @var string
	 */
	protected $table = 'dish_ratings';

	/**
	 * @var array
	 */
	protected $fillable = [
		'taste_rating',
		'visual_rating',
		'quantity_rating',
		'comment',
		'dish_id',
		'user_id',
		'order_item_id'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function dish()
	{
		return $this->belongsTo('App\Models\Dish');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function orderItem()
	{
		return $this->belongsTo('App\Models\OrderItem');
	}

	/**
	 * @param Model $dish
	 * @param $data
	 * @param Model $user
	 * @return static
	 */
	public function createRating(Model $dish, $data, Model $user)
	{
		$rating = new static();
		$rating->fill(array_merge($data, [
			'user_id' => $user->id,
		]));

		$dish->ratings()->save($rating);

		return $rating;
	}

	/**
	 * @param Model $dish
	 * @param $data
	 * @param Model $user
	 * @return array
	 */
	public function createUniqueRating(Model $dish, $data, Model $user)
	{
		$rating = [
			'user_id' => $user->id,
			"dish_id" => $dish->id,
		];

		Rating::updateOrCreate($rating, $data);
		return $rating;
	}

	/**
	 * @param $id
	 * @param $data
	 * @return mixed
	 * @throws YechefException
	 */
	public function updateRating($id, $data)
	{
		try {
			$rating = static::findOrFail($id);
			$rating->update($data);
			return $rating;
		} catch (ModelNotFoundException $e) {
			throw new YechefException(11501);
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 * @throws YechefException
	 */
	public function deleteRating($id)
	{
		try {
			return static::findOrFail($id)->delete();
		} catch (ModelNotFoundException $e) {
			throw new YechefException(11501);
		}
	}

	/**
	 * @param null $id
	 * @return array
	 */
	public static function getValidationRule($id = null)
	{
		$rule = [
			'dishId'          => 'bail|required',
			'taste_rating'    => 'bail|required|integer|between:1,5',
			'visual_rating'   => 'bail|required|integer|between:1,5',
			'quantity_rating' => 'bail|required|integer|between:1,5',
			'comment'         => 'required|max:200',
		];

		if (!$id) {
			$rule['orderItemId'] = 'required';
		}

		Return $rule;
	}

}