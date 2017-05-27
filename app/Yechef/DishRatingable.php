<?php

namespace App\Yechef;

use App\Models\DishRating as Rating;
use Illuminate\Database\Eloquent\Model;

trait DishRatingable
{
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function ratings()
	{
		return $this->morphMany(Rating::class, 'ratingable');
	}

	public function author()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 *
	 * @return mix
	 */
	public function avgRating()
	{
		return collect(
			[
				'taste_rating'    => $this->ratings()->avg('taste_rating'),
				'visual_rating'   => $this->ratings()->avg('visual_rating'),
				'quantity_rating' => $this->ratings()->avg('quantity_rating'),
			]);
	}

	/**
	 *
	 * @return mix
	 */
	public function sumRating()
	{
		return collect(
			[
				'taste_rating'    => $this->ratings()->sum('taste_rating'),
				'visual_rating'   => $this->ratings()->sum('visual_rating'),
				'quantity_rating' => $this->ratings()->sum('quantity_rating'),
			]);
	}

	/**
	 * @param $max
	 *
	 * @return mix
	 */
	public function ratingPercent($max = 5)
	{
		$quantity = $this->ratings()->count();
		$total = $this->sumRating();
		return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
	}

	/**
	 *
	 * @return mix
	 */
	public function countPositive()
	{
		return collect([
			'taste_rating'    => $this->ratings()->where('taste_rating', '>', '0')->count(),
			'visual_rating'   => $this->ratings()->where('visual_rating', '>', '0')->count(),
			'quantity_rating' => $this->ratings()->where('quantity_rating', '>', '0')->count(),
		]);
	}

	/**
	 *
	 * @return mix
	 */
	public function countNegative()
	{
		$taste_quantity = $this->ratings()->where('taste_rating', '<', '0')->count();
		$visual_quantity = $this->ratings()->where('visual_rating', '<', '0')->count();
		$quantity_quantity = $this->ratings()->where('quantity_rating', '<', '0')->count();
		return collect([
			'taste_rating'    => ("-$taste_quantity"),
			'visual_rating'   => ("-$visual_quantity"),
			'quantity_rating' => ("-$quantity_quantity"),
		]);
	}

	/**
	 * @param $data
	 * @param Model $author
	 * @param Model|null $parent
	 *
	 * @return static
	 */
	public function rating($data, Model $author, Model $parent = null)
	{
		return (new Rating())->createRating($this, $data, $author);
	}

	/**
	 * @param $data
	 * @param Model $author
	 * @param Model|null $parent
	 *
	 * @return static
	 */
	public function ratingUnique($data, Model $author, Model $parent = null)
	{
		return (new Rating())->createUniqueRating($this, $data, $author);
	}

	/**
	 * @param $id
	 * @param $data
	 * @param Model|null $parent
	 *
	 * @return mixed
	 */
	public static function updateRating($id, $data, Model $parent = null)
	{
		return (new Rating())->updateRating($id, $data);
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function deleteRating($id)
	{
		return (new Rating())->deleteRating($id);
	}

	public function getAvgRatingAttribute()
	{
		return $this->avgRating();
	}

	public function getratingPercentAttribute()
	{
		return $this->ratingPercent();
	}

	public function getSumRatingAttribute()
	{
		return $this->sumRating();
	}

	public function getCountPositiveAttribute()
	{
		return $this->countPositive();
	}

	public function getCountNegativeAttribute()
	{
		return $this->countNegative();
	}
}
