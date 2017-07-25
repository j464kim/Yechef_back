<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $validator;

	/**
	 * Controller constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * This method returns the user specified by the Request's userId parameter.
	 * If the userId param is not provided, the currently logged in user will be returned
	 * @param Request $request
	 * @return mixed
	 * @throws YechefException
	 */
	public function getUser(Request $request)
	{
		try {
			$user = null;
			if ($request->userId) {
				$user = User::findById($request->userId);
			} else {
				$user = $request->user();
			}
		} catch (\Exception $e) {
			throw new YechefException(15502, $e->getMessage());
		}

		return $user;
	}

	/**
	 * @param Request $request
	 * @param $validationRule
	 * @throws YechefException
	 */
	public function validateInput(Request $request, $validationRule)
	{
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(0, $message);
		}
	}
}
