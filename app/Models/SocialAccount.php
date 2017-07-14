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
				$first_name = '';
				$last_name = '';
				switch ($provider) {
					case 'google':
						$first_name = $providerUser->user['name']['givenName'];
						$last_name = $providerUser->user['name']['familyName'];
						break;
					case 'facebook':
						$first_name = $providerUser->user['first_name'];
						$last_name = $providerUser->user['last_name'];
						break;
					default:

				}
				$user = User::create([
					'email'      => $providerUser->getEmail(),
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'password'   => Hash::make(md5(time())),
					'show_phone' => '1',
					'show_forks' => '1',
					'show_subscription' => '1',
				]);

				$account->user()->associate($user);
				$account->save();
			}

			return $user;

		}

	}
}
