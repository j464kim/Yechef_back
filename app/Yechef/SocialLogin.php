<?php

namespace App\Yechef;

use Adaojunior\Passport\SocialGrantException;
use Adaojunior\Passport\SocialUserResolverInterface;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;

class SocialLogin implements SocialUserResolverInterface
{
	/**
	 * Resolves user by given provider and access token.
	 *
	 * @param string $provider
	 * @param string $accessToken
	 * @param null $accessTokenSecret
	 * @return \Illuminate\Contracts\Auth\Authenticatable
	 * @throws SocialGrantException
	 * @internal param string $network
	 */
	public function resolve($provider, $accessToken, $accessTokenSecret = null)
	{
		$providerUser = null;

		switch ($provider) {
			case 'google':
				$providerUser = Socialite::driver($provider)->userFromToken($accessToken);
				break;
			case 'facebook':
				$providerUser = Socialite::driver($provider)->fields([
					'name',
					'first_name',
					'last_name',
					'email',
					'gender',
					'verified'
				])->userFromToken($accessToken);
				break;
			default:
				$providerUser = Socialite::driver($provider)->userFromToken($accessToken);
				break;
		}
		return SocialAccount::createOrGetUser($provider, $providerUser);
	}
}