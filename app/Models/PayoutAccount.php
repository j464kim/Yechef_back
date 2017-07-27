<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutAccount extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'user_id',
		'connect_id',
		'country'
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public static function getValidationRule()
	{
		$rule = array(

		);

		return $rule;
	}
}