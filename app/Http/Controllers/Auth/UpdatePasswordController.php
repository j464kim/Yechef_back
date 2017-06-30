<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\YechefException;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;


class UpdatePasswordController extends Controller
{
	private $hash;

	public function __construct(Application $app)
	{
		parent::__construct($app);

		$this->hash = $app->make('hash');
	}

	/**
	 * Update the password for the user.
	 *
	 * @param Request $request
	 */
	public function update(Request $request)
	{
		$validationRule = User::getPasswordValidationRule();
		$this->validateInput($request, $validationRule);

		$user = $this->getUser($request);
		$passwordInDatabase = $user->password;

		if ($this->hash->check($request->oldPassword, $passwordInDatabase)) {
			//Change the password
			$user->fill([
				'password' => $this->hash->make($request->newPassword)
			])->save();
		} else {
			throw new YechefException(10506);
		}
	}
}