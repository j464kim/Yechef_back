<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['kitchen_id', 'active', 'day', 'open_time', 'close_time'];

	public function kitchen()
	{
		return $this->belongsTo('App\Models\Kitchen');
	}
}