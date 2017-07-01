<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\YechefException;
use App\Models\Dish;


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

	public function getLoggedInUser(Request $request)
	{
		$user = $request->user();
		return response()->success($user);
	}

	public function index(Request $request)
	{
		$result = User::all();
		return response()->success($result);
	}

	public function checkOwnership(Request $request)
	{
		if ($dishId = $request->input('dish_id')) {
			$dish = Dish::findDish($dishId);
			$kitchenId = $dish->kitchen_id;
		} else {
			$kitchenId = $request->input('kitchen_id');
		}

		try {
			$request->user()->isVerifiedKitchenOwner($kitchenId);
		} catch (YechefException $e) {
			return response()->notallow($e->getMessage());
		}
		return response()->success();
	}

}
