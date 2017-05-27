<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{

	/**
	 * Get all of the owning reactionable models.
	 */
	public function reactionable()
	{
		return $this->morphTo();
	}

	/**
	 * @var array
	 */
	protected $fillable = [
		'kind',
		'user_id',
		'reactionable_id',
		'reactionable_type',
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
			'reactionableId'   => 'bail|required',
			'reactionableType' => 'bail|required',
			'kind'             => 'required'
		);

		return $rule;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public static function findReaction($id)
	{
		try {
			return Reaction::findOrFail($id);
		} catch (\Exception $e) {
			throw new YechefException(14501);
		}
	}

	/**
	 * @return int
	 */
	public function getTotalLikes()
	{
		$totalLikes = $this::where('kind', '=', 1)->get();

		return count($totalLikes);
	}

	/**
	 * @return int
	 */
	public function getTotalDislikes()
	{
		$totalDislikes = $this::where('kind', '=', 0)->get();

		return count($totalDislikes);
	}
}
