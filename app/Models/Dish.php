<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['slug', 'name', 'description'];

	/**
	 * Many to many relationship to media
	 */
	public function media()
	{
		return $this->belongsToMany('App\Models\Media');
	}

}
