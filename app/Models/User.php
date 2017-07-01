<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Traits\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable;
	use CanResetPassword;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'phone',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	public static function getValidation()
	{
		return [
			'first_name' => 'required|max:255',
			'last_name'  => 'required|max:255',
			'email'      => 'required|email|max:255|unique:users',
			'password'   => 'required|min:6|confirmed',
			'phone'      => 'phone',
		];
	}

	public function kitchens()
	{
		return $this->belongsToMany('App\Models\Kitchen')->withPivot('role', 'verified')->withTimestamps();
	}

	public function reactions()
	{
		return $this->hasMany('App\Models\Reaction');
	}


	public static function findUser($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return User::with('medias')->findOrFail($id);
			} else {
				return User::findOrFail($id);
			}
		} catch (\Exception $e) {
			throw new YechefException(15501);
		}
	}

	public function isVerifiedKitchenOwner($kitchenId)
	{
		$kitchenWithPivot = $this->kitchens()->wherePivot('kitchen_id', $kitchenId);
		$verifiedKitchen = $kitchenWithPivot->wherePivot('verified', true)->get();

		if ($kitchenWithPivot->get()->isEmpty()) {
			throw new YechefException(12506);
		} elseif ($verifiedKitchen->isEmpty()) {
			throw new YechefException(12505);
		}
	}
}