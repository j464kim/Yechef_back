<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\OauthException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Validator;

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

	const REFRESH_TOKEN = 'refreshToken';

	private $guzzleClient;
	private $cookie;

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
	 */
    public function __construct(Application $app, Client $guzzleClient)
    {
		$this->guzzleClient = $guzzleClient;
		$this->cookie = $app->make('cookie');
        $this->middleware('guest', ['except' => 'logout']);
    }

	/**
	 * Login the user using Oauth password granted token
	 *
	 * @param Request $request
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->fail($validator->errors()->first());
		}

		$username = request()->input('username');
		$password = request()->input('password');

		return $this->proxy('password', [
			'username' => $username,
			'password' => $password
		]);
	}

	/**
	 * Refresh access token
	 *
	 * @param Request $request
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	public function refreshToken(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'refresh_token' => 'required'
		]);

		if ($validator->fails()) {
			return response()->fail($validator->errors()->first());
		}

		$refreshToken = request()->input('refresh_token');
		return $this->proxy('refresh_token', [
			'refresh_token' => $refreshToken,
		]);
	}

	/**
	 * proxy to oauth/token to bypass cors filter and hide client credentials to server side
	 * Also stores the refresh token in session cookie
	 *
	 * @param $grantType
	 * @param array $data
	 * @param string $scope
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 * @throws OauthException
	 */
	private function proxy($grantType, array $data = [], $scope='*')
	{
		// TODO scope is for future permission use

		try {
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

			return $response;
		} catch (\Exception $e) {
			throw new OauthException($e->getMessage());
		}
	}
}
