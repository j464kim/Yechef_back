<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;


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
		parent::__construct($app);

		$this->guzzleClient = $guzzleClient;
		$this->cookie = $app->make('cookie');
		$this->validator = $app->make('validator');
		$this->auth = $app->make('auth');
		$this->db = $app->make('db');
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
		$validationRule = array(
			'email'    => 'email|required',
			'password' => 'required'
		);
		$this->validateInput($request, $validationRule);

		$email = $request->input('email');
		$password = $request->input('password');

		try {
			$user = User::whereEmail($email)->firstOrFail();
		} catch (ModelNotFoundException $e) {
			throw new YechefException(10501);
		}

		// check if the user's email is verified yet
		if (!$user->isVerified()) {
			return response()->fail(10500);
		}

		// grant access token
		try {
			$result = $this->proxy('password', [
				'username' => $email,
				'password' => $password
			]);

		} catch (\Exception $e) {
			log::error($e->getMessage());
			log::error($e->getTraceAsString());
			log::error($e->getFile());
			throw new YechefException(10501);
		}

		return response()->success($result, 10000);

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

		$refreshToken = $request->input('refresh_token');
		try {
			$result = $this->proxy('refresh_token', [
				'refresh_token' => $refreshToken,
			]);
			// use trans
			return response()->success($result, 'access token refreshed');
		} catch (\Exception $e) {
			throw new YechefException(10503, $e->getMessage());
		}
	}

	public function Logout()
	{
		$user = $this->auth->user();

		if (!isset($user)) {
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
	private function proxy($grantType, array $data = [], $scope = '')
	{
		// TODO scope is for future permission use

		$dataCopy = $data;
		$data = array_merge($data, [
			'client_id'     => env('PASSWORD_CLIENT_ID'),
			'client_secret' => env('PASSWORD_CLIENT_SECRET'),
			'grant_type'    => $grantType,
			'scope'         => $scope
		]);

		$response = $this->guzzleClient->request('POST', url('oauth/token'), [
			'form_params' => $data
		])->getBody();
		$data = json_decode($response, true);

		// Create a refresh token cookie
		$this->cookie->queue(
			self::REFRESH_TOKEN,
			isset($data['refresh_token']) ? $data['refresh_token'] : null,
			$data['expires_in'],
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

		$response = $this->socialite::driver('facebook')->getAccessTokenResponse($code);

		$accessToken = $response['access_token'];

		$result = $this->proxy('social', [
			'network'      => 'facebook',
			'access_token' => $accessToken,
		]);

		return response()->success($result, 10000);
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

		$response = $this->socialite::driver('google')->getAccessTokenResponse($code);

		$accessToken = $response['access_token'];

		$result = $this->proxy('social', [
			'network'      => 'google',
			'access_token' => $accessToken,
		]);

		return response()->success($result, 10000);
	}
}
