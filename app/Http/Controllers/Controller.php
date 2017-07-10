<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Application;
use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Facades\Log;


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
	 * @param Request $request
	 * @return mixed
	 * @throws YechefException
	 */
	public function getUser(Request $request)
	{
		try {
			$user = $request->user();
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
