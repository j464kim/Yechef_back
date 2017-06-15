<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
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

	public function index(Request $request)
	{
		$result = User::all();
		return response()->success($result);
	}

}
