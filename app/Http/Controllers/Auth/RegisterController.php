<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppMailer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	// DI parameters
	private $loginCtrl;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Application $app, LoginController $loginController)
	{
		parent::__construct($app);

		$this->loginCtrl = $loginController;
		$this->middleware('guest');
	}

	public function register(Request $request)
	{
		$validationRule = User::getValidationRule();
		$this->validateInput($request, $validationRule);

		$first_name = $request->input('first_name');
		$last_name = $request->input('last_name');
		$email = $request->input('email');
		$password = $request->input('password');
		$phone = $request->input('phone');

		$user = User::create([
			'email'      => $email,
			'password'   => bcrypt($password),
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'phone'      => $phone,
			'show_phone' => true,
			'show_forks' => true,
			'show_subscription' => true,
		]);

		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @param AppMailer $mailer
	 */
	public function sendEmailVerifyLink(Request $request, AppMailer $mailer)
	{
		try {
		$userToVerify = User::whereEmail($request->email)->firstOrFail();
		} catch (\Exception $e) {
			throw new YechefException(10508);
		}

		// if email is unique, send email verification link
		$mailer->sendConfirmationEmailTo($userToVerify);

		return response()->success($userToVerify, 10004);
	}

	/**
	 * @param $token
	 * @throws YechefException
	 */
	public function confirmEmail($token)
	{
		try {
			$user = User::whereToken($token)->firstOrFail();
		} catch (\Exception $e) {
			throw new YechefException(10507);
		}

		$user->approveEmail();

		return response()->success($user, 10005);
	}
}