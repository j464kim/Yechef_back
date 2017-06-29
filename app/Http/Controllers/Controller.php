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

	public function __construct(Application $app)
	{
		Log::info('base constructor');

		$this->validator = $app->make('validator');
	}

	public function validateInput(Request $request, $validationRule)
	{
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException($message);
		}
	}
}
