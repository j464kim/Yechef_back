<?php

namespace App\Models\Rating;

use Ghanem\Rating\Models\Rating;
use Illuminate\Database\Eloquent\Model;

class DishRating extends Rating
{
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
		'ratingable_id',
		'ratingable_type',
		'author_id',
		'author_type'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function rateable()
	{
		return $this->morphTo('');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function author()
	{
		return $this->morphTo('author');
	}

	/**
	 * @param Model $ratingable
	 * @param $data
	 * @param Model $author
	 *
	 * @return static
	 */
	public function createRating(Model $ratingable, $data, Model $author)
	{
		$rating = new static();
		$rating->fill(array_merge($data, [
			'author_id'   => $author->id,
			'author_type' => get_class($author),
		]));

		$ratingable->ratings()->save($rating);

		return $rating;
	}

	/**
	 * @param Model $ratingable
	 * @param $data
	 * @param Model $author
	 *
	 * @return static
	 */
	public function createUniqueRating(Model $ratingable, $data, Model $author)
	{
		$rating = [
			'author_id'       => $author->id,
			'author_type'     => get_class($author),
			"ratingable_id"   => $ratingable->id,
			"ratingable_type" => get_class($ratingable),
		];

		Rating::updateOrCreate($rating, $data);
		return $rating;
	}

	/**
	 * @param $id
	 * @param $data
	 *
	 * @return mixed
	 */
	public function updateRating($id, $data)
	{
		$rating = static::find($id);
		$rating->update($data);

		return $rating;
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deleteRating($id)
	{
		return static::find($id)->delete();
	}

	public static function getValidation($id = null)
	{
		Return [
			'dishId'          => 'bail|required',
			'taste_rating'    => 'bail|required|numeric',
			'visual_rating'   => 'bail|required|numeric',
			'quantity_rating' => 'bail|required|numeric',
			'comment'         => 'required|max:200',
		];
	}

	public static function findDishRating($id)
	{
		try {
			return DishRating::findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error('Could not find the dish_rating with id: ' . $id);
			throw new YechefException(11503);
		}
	}
}