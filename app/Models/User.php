<?php

namespace App\Models;

use App\Traits\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Exceptions\YechefException;

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

	public function cart()
	{
		return $this->hasOne('App\Models\Cart');
	}

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


	/**
	 * @param $id
	 * @param bool $withMedia
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
	 * @throws YechefException
	 */
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

	/**
	 * check if current user has an existing cart, otherwise create one
	 *
	 * @return false|\Illuminate\Database\Eloquent\Model
	 * @throws YechefException
	 */
	public function getCart()
	{
		try {
			$cart = $this->cart ?: $this->cart()->save(new Cart);
		} catch (\Exception $e) {
			throw new YechefException(18502);
		}

		return $cart;
	}
}