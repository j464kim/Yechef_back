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

	/**
	 * @return array
	 */
	public static function getValidationRule($userId = null)
	{
		$rule = array(
			'first_name' => 'required|max:255',
			'last_name'  => 'required|max:255',
			'phone'      => 'phone',
		);

		// For Update
		if (!$userId) {
			$rule['email'] = 'required|email|max:255|unique:users,email';
			$rule['password'] = 'required|min:6|confirmed';
		}

		return $rule;
	}

	public static function getPasswordValidationRule()
	{
		$rule = array(
			'oldPassword' => 'required',
			'newPassword' => 'required|min:6|confirmed',
		);

		return $rule;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function kitchens()
	{
		return $this->belongsToMany('App\Models\Kitchen')->withPivot('role', 'verified')->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function reactions()
	{
		return $this->hasMany('App\Models\Reaction');
	}


	/**
	 * @param $id
	 * @param bool $withMedia
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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

	public function getSubscriptions()
	{
		$subscriptionKitchens = Kitchen::with('medias')
			->join('reactions', 'reactions.reactionable_id', '=', 'kitchens.id')
			->where('user_id', $this->id)
			->where('kind', Reaction::SUBSCRIBE)
			->select('kitchens.*')
			->get();

		return $subscriptionKitchens;
	}

	public function getForkedDishes()
	{
		$forkedDishes = Dish::with('medias')
			->join('reactions', 'reactions.reactionable_id', '=', 'dishes.id')
			->where('user_id', $this->id)
			->where('kind', Reaction::FORK)
			->select('dishes.*')
			->get();

		return $forkedDishes;
	}

}