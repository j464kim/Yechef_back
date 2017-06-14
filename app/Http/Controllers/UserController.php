<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Application;
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
	public function __construct(Application $app, Request $request)
	{
		$this->auth = $app->make('auth');
	}

	public function getMyKitchens(Request $request)
	{
		$this->user = $request->user();
		$result = $this->user->kitchens()->with('medias')->get();
		return response()->success($result);
	}

	public function index(Request $request)
	{
		$result = User::all();
		return response()->success($result);
	}

}
