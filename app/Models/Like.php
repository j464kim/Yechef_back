<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{

	/**
	 * Get all of the owning likable models.
	 */
	public function likable()
	{
		return $this->morphTo();
	}

	/**
	 * @var array
	 */
	protected $fillable = [
		'isLike',
		'user_id',
		'likable_id',
		'likable_type',
	];

	/**
	 * Get the user that owns the like.
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'likableId' => 'bail|required',
			'isLike'    => 'required'
		);

		return $rule;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public static function findLike($id)
	{
		try {
			return Like::findOrFail($id);
		} catch (\Exception $e) {
			throw new YechefException(14501);
		}
	}

	/**
	 * @return int
	 */
	public function getTotalLikes()
	{
		$totalLikes = $this::where('isLike', '=', 1)->get();

		return count($totalLikes);
	}

	/**
	 * @return int
	 */
	public function getTotalDislikes()
	{
		$totalDislikes = $this::where('isLike', '=', 0)->get();

		return count($totalDislikes);
	}
}
