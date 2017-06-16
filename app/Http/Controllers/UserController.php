<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\YechefException;
use Illuminate\Foundation\Application;


class UserController extends Controller
{

	/**
	 * UserController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getMyKitchens(Request $request)
	{
		$user = $request->user();
		try {
			$result = $user->kitchens()->with('medias')->get();
		} catch (Exception $e) {
			return response()->fail($e->getMessage());
		}
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getLoggedInUser(Request $request)
	{
		$user = $request->user();
		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function index()
	{
		$result = User::all();
		return response()->success($result);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id)
	{
		$user = User::findUser($id);

		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return mixed
	 */
	public function update(Request $request, $id)
	{
		$this->validateInput($request);

		$user = User::findUser($id);

		$user->update(
			[
				'first_name' => $request->input('first_name'),
				'last_name'  => $request->input('last_name'),
				'email'      => $request->input('email'),
				'password'   => $request->input('password'),
				'phone'      => $request->input('phone'),
			]
		);

		return response()->success($user, 15001);
	}


	/**
	 * @param Request $request
	 */
	private function validateInput(Request $request)
	{
		$validationRule = User::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(15502, $message);
		}
	}
}
