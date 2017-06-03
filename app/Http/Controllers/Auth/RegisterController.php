<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
	private $validator;

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
		$this->loginCtrl = $loginController;
		$this->middleware('guest');
		$this->validator = $app->make('validator');
	}

	public function register(Request $request)
	{
		$validator = $this->validator->make($request->all(), User::getValidation());

		if ($validator->fails()) {
			throw new YechefException(10505, $validator->getMessageBag());
		}

		$first_name = $request->input('first_name');
		$last_name = $request->input('last_name');
		$email = $request->input('email');
		$password = $request->input('password');
		$phone = $request->input('phone');

		User::create([
			'email'      => $email,
			'password'   => bcrypt($password),
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'phone'      => $phone,
		]);

		return $this->loginCtrl->login($request);
	}
}