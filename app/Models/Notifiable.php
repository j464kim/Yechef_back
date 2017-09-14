<?php

namespace App\Models;

use App\Traits\ModelService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Notifiable
 * The notifiable class is storing status of broadcasted events.
 * @package App\Models
 */
class Notifiable extends Model
{
	use ModelService, SoftDeletes;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'notifications';

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get all of the owning notifiable models.
	 */
	public function notifiable()
	{
		return $this->morphTo();
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'status',
		'notifiable_id',
		'notifiable_type',
	];

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		return array(
			'status' => 'required'
		);
	}
}