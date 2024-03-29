<?php

namespace App\Models;

use App\Traits\ModelService;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
	use ModelService;

	const DISLIKE = 0;
	const LIKE = 1;
	const FORK = 2;
	const SUBSCRIBE = 3;

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

}
