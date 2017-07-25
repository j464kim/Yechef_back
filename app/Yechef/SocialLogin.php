<?php

namespace App\Yechef;

use Adaojunior\Passport\SocialGrantException;
use Adaojunior\Passport\SocialUserResolverInterface;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;

class SocialLogin implements SocialUserResolverInterface
{
	private $socialite;

	public function __construct(Socialite $socialite)
	{
		$this->socialite = $socialite;
	}

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
				$providerUser = $this->socialite::driver($provider)->userFromToken($accessToken);
				break;
			case 'facebook':
				$providerUser = $this->socialite::driver($provider)->fields([
					'name',
					'first_name',
					'last_name',
					'email',
					'gender',
					'verified'
				])->userFromToken($accessToken);
				break;
			default:
				$providerUser = $this->socialite::driver($provider)->userFromToken($accessToken);
				break;
		}
		return SocialAccount::createOrGetUser($provider, $providerUser);
	}
}