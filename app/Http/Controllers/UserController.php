<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
	private $auth;
	private $user;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->auth = $app->make('auth');
		//TODO: Get User Dynamically
		$this->user = User::first();
//		$this->user = $this->auth->user();
	}

	public function getMyKitchens(Request $request)
	{
		$result = $this->user->kitchens()->with('medias')->get();
		return response()->success($result);
	}
}
