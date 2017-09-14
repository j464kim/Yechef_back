<?php

namespace App\Models;

use App\Traits\ModelService;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MessageRoom
 * @package App\Models
 */
class MessageRoom extends Model
{
	use SoftDeletes, CascadeSoftDeletes, ModelService;

	/**
	 * Enable softDeletes & cascade soft-deletes
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Cascade soft-deletes related models
	 */
	protected $cascadeDeletes = ['messages'];

	public function messages()
	{
		return $this->hasMany(Message::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class);
	}
}
