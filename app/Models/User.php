<?php

namespace App\Models;

use App\Exceptions\YechefException;
use App\Traits\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\ModelService;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable;
	use CanResetPassword, ModelService;

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
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function carts()
	{
		return $this->hasMany('App\Models\Cart');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function payment()
	{
		return $this->hasOne('App\Models\Payment');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function transactions()
	{
		return $this->hasMany('App\Models\Transaction');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function orders()
	{
		return $this->hasMany('App\Models\Order');
	}

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

	/**
	 * @return array
	 */
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

	public function isOrderMaker($orderId)
	{
		if (Order::findById($orderId)->user_id != $this->id) {
			throw new YechefException(20501);
		}
	}

	/**
	 * check if current user has an existing cart, otherwise create one
	 * retrieve cart depending on which kitchen the added dish is coming from
	 *
	 * @param null $kitchenId
	 * @return false|\Illuminate\Database\Eloquent\Model|mixed
	 * @throws YechefException
	 */
	public function getCart($kitchenId = null)
	{
		$carts = $this->carts();

		// for Index, retrieve every cart of every kitchen
		if (!$kitchenId) {

			$carts = $carts->with('items')->get();
			return $carts;
		}

		$carts = $carts->get();
		// for Store & Update & Destroy
		// if cart belonging to this kitchen does not exist yet,
		if ($carts->isEmpty() || !$carts->contains('kitchen_id', $kitchenId)) {

			$cart = new Cart([
				'kitchen_id'  => $kitchenId,
			]);
			$cart = $this->carts()->save($cart);
		}

		if ($carts->contains('kitchen_id', $kitchenId)) {

			// if cart of the kitchen id already exists, retrieve that cart
			$cart = $carts->where('kitchen_id', $kitchenId)->first();
		}

		return $cart;

	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
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

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
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

	/**
	 * Set a unique token to be used for verifying email
	 */
	public static function boot()
	{
		// listen to any model event that will be fired
		parent::boot();

		// listen for a new record being created
		static::creating(function ($user) {
			$user->token = str_random(30);
		});
	}

	/**
	 * Set the verified flag to true
	 */
	public function approveEmail()
	{
		$this->verified = true;
		$this->token = null;
		$this->save();
	}

	/**
	 * Check if the account email is verified
	 * @return mixed
	 */
	public function isVerified()
	{
		return $this->verified;
	}

}