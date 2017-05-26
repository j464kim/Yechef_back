<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccount extends Model
{
	protected $fillable = ['user_id', 'provider_user_id', 'provider'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * create or return user
	 *
	 * @param $provider
	 * @param ProviderUser $providerUser
	 * @return mixed
	 */
	public static function createOrGetUser($provider, ProviderUser $providerUser)
	{
		$account = SocialAccount::where('provider', $provider)
			->where('provider_user_id', $providerUser->getId())
			->first();

		if ($account) {
			return $account->user;
		} else {

			$account = new SocialAccount([
				'provider_user_id' => $providerUser->getId(),
				'provider'         => $provider
			]);

			$user = User::where('email', $providerUser->getEmail())->first();

			if (!$user) {
// TODO: create user account based on these data. create random password, send email in event
				$user = User::create([
					'email'      => $providerUser->getEmail(),
					'first_name' => $providerUser->user['name']['givenName'],
					'last_name'  => $providerUser->user['name']['familyName'],
					'password'   => Hash::make(md5(time()))
				]);
			}

			$account->user()->associate($user);
			$account->save();

			return $user;

		}

	}
}
