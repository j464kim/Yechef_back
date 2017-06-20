<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;


class UpdatePasswordController extends Controller
{
	/**
	 * UpdatePasswordController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * Update the password for the user.
	 *
	 * @param Request $request
	 */
	public function update(Request $request)
	{
		$this->validateInput($request);

		$user = $request->user();
		$passwordInDatabase = $user->password;

		if (Hash::check($request->oldPassword, $passwordInDatabase)) {
			//Change the password
			$user->fill([
				'password' => Hash::make($request->newPassword)
			])->save();
		} else {
			throw new YechefException(10506);
		}
	}

	/**
	 * @param Request $request
	 * @throws YechefException
	 */
	private function validateInput(Request $request)
	{
		$validationRule = array(
			'oldPassword' => 'required',
			'newPassword' => 'required|min:6|confirmed',
		);

		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(10507);
		}
	}
}