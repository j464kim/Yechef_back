<?php

namespace App\Models;

use App\Traits\ModelService;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Message
 * @package App\Models
 */
class Message extends Model
{
	use SoftDeletes, ModelService, CascadeSoftDeletes;

	protected $cascadeDeletes = ['notifiable'];

	protected $fillable = [
		'user_id',
		'message_room_id',
		'message_body'
	];

	/**
	 * Enable softDeletes & cascade soft-deletes
	 */
	protected $dates = ['deleted_at'];

	public function messageRoom()
	{
		return $this->belongsTo(MessageRoom::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function notifiable()
	{
		return $this->morphOne(Notifiable::class, 'notifiable');
	}

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'messageBody'   => 'bail|required',
			'messageRoomId' => 'required|integer'
		);

		return $rule;
	}
}
