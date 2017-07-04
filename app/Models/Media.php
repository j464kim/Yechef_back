<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Media extends Model
{
	use SoftDeletes;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'media';

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get all of the owning mediable models.
	 */
	public function mediable()
	{
		return $this->morphTo();
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'slug',
		'url',
		'type',
		'mediable_id',
		'mediable_type'
	];

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'file' => 'required'
		);

		return $rule;
	}
}
