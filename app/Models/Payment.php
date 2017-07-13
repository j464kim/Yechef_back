<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'user_id',
		'stripe_id',
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

}