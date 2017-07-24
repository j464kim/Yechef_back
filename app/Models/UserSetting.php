<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserSetting
 * @package App
 */
class UserSetting extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'show_phone',
		'show_forks',
		'show_subscription',
	];

	/**
	 * @var array
	 */
	protected $attributes = [
		'show_phone'        => true,
		'show_forks'        => true,
		'show_subscription' => true,
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
			'show_phone'        => 'required',
			'show_forks'        => 'required',
			'show_subscription' => 'required',
		);

		return $rule;
	}
}
