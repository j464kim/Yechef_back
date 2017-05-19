<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    // cookie key
	const REFRESH_TOKEN = 'refreshToken';

	// DI parameters
	private $guzzleClient;
	private $cookie;
	private $validator;
	private $auth;
	private $db;
	private $socialite;
	private $hash;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @param Application $app
	 * @param Client $guzzleClient
	 * @param Socialite $socialite
	 */
    public function __construct(Application $app, Client $guzzleClient, Socialite $socialite)
    {
		$this->guzzleClient = $guzzleClient;
		$this->cookie = $app->make('cookie');
		$this->validator = $app->make('validator');
		$this->auth = $app->make('auth');
		$this->db = $app->make('db');
		$this->hash = $app->make('hash');
		$this->socialite = $socialite;
        $this->middleware('guest', ['except' => 'logout']);
    }

	/**
	 * Login the user using Oauth password granted token
	 *
	 * @param Request $request
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 * @throws YechefException
	 */
	public function login(Request $request)
	{
		$validator = $this->validator->make($request->all(), [
			'username' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			throw new YechefException(10500);
		}

		$username = request()->input('username');
		$password = request()->input('password');


		try{
			$result = $this->proxy('password', [
				'username' => $username,
				'password' => $password
			]);

			return response()->success($result, 10000);
		}catch(\Exception $e) {
			throw new YechefException(10501);
		}
	}

	/**
	 * Refresh access token
	 *
	 * @param Request $request
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 * @throws YechefException
	 */
	public function refreshToken(Request $request)
	{
		$validator = $this->validator->make($request->all(), [
			'refresh_token' => 'required'
		]);

		if ($validator->fails()) {
			throw new YechefException(10502);
		}

		$refreshToken = request()->input('refresh_token');
		try{
			$result = $this->proxy('refresh_token', [
				'refresh_token' => $refreshToken,
			]);
			// use trans
			return response()->success($result, 'access token refreshed');
		}catch(\Exception $e) {
			throw new YechefException(10503);
		}
	}

	public function Logout()
	{
		$user = $this->auth->user();

		if( !isset($user) ) {
			throw new YechefException(10504);
		}

		$accessToken = $user->token();

		$this->db
			->table('oauth_refresh_tokens')
			->where('access_token_id', $accessToken->id)
			->update([
				'revoked' => true
			]);

		$accessToken->revoke();

		$this->cookie->queue($this->cookie->forget(self::REFRESH_TOKEN));

		return response()->success(10002);
	}

	/**
	 * proxy to oauth/token to bypass cors filter and hide client credentials to server side
	 * Also stores the refresh token in session cookie
	 *
	 * @param $grantType
	 * @param array $data
	 * @param string $scope
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	private function proxy($grantType, array $data = [], $scope='*')
	{
		// TODO scope is for future permission use

		$data = array_merge($data, [
			'client_id'     => env('PASSWORD_CLIENT_ID'),
			'client_secret' => env('PASSWORD_CLIENT_SECRET'),
			'grant_type'    => $grantType,
			'scope'			=> $scope
		]);

		$response = $this->guzzleClient->request('POST', url('oauth/token'), [
			'form_params' => $data
		])->getBody();
		$data = json_decode($response);

		// Create a refresh token cookie
		$this->cookie->queue(
			self::REFRESH_TOKEN,
			$data->refresh_token,
			$data->expires_in,
			null,
			null,
			false,
			true // HttpOnly
		);
		return $data;
	}

	/**
	 * controller for facebook oauth login callback
	 *
	 * @param Request $request
	 * @return mixed
	 */
	public function facebook(Request $request)
	{
		$code = $request->input('code');

		$token = $this->socialLogin('facebook', $code);

		return response()->success($token);
	}


	/**
	 * controller for google oauth login callback
	 *
	 * @param Request $request
	 * @return mixed
	 */
	public function google(Request $request)
	{
		$code = $request->input('code');

		$token = $this->socialLogin('google', $code);

		return response()->success($token);
	}

	/**
	 * Login user and return access token
	 *
	 * @param $provider
	 * @param $code
	 * @return array
	 */
	private function socialLogin($provider, $code)
	{
		$response = $this->socialite::driver($provider)->getAccessTokenResponse($code);

		$token = $response['access_token'];

		$user = $this->socialite::driver($provider)->userFromToken($token);

		$name = $user->getName();
		$email = $user->getEmail();

		// TODO: create user account based on these data. create random password, send email in event
		// TODO: use the proxy defined above to return correct token format
		// TODO: store cookie in cache, or use the proxy method to handle this
		$user = User::where('email', $email)->first();

		// return user token when the user email exists
		if(isset($user)) {
			return $this->issueToken($user);
		}
		// otherwise create the user and return the token

		$user = User::create([
			'name' => $name,
			'email' => $email,
			'password' => $this->hash->make(md5(time()))
		]);

		return $this->issueToken($user);

	}

	/**
	 * Issue access token
	 *
	 * @param User $user
	 * @return array
	 */
	private function issueToken(User $user) {
        $userToken = $user->token() ?? $user->createToken(env('PERSONAL_CLIENT_TOKEN_NAME'));

        return [
            "token_type" => "Bearer",
            "access_token" => $userToken->accessToken
        ];
    }
}
