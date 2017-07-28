<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ModelService;

class PayoutAccount extends Model
{
	use SoftDeletes;
	use modelService;

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
			'state'       => 'bail|required',
			'city'        => 'required',
			'line1'       => 'required',
			'postal_code' => 'required',
		);

		return $rule;
	}
}