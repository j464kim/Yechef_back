<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Kitchen;
use Illuminate\Http\Request;
use App\Exceptions\YechefException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getMyKitchens(Request $request)
	{
		$user = $this->getUser($request);

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
		$user = $this->getUser($request);

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

	public function checkOwnership(Request $request)
	{
		if ($dishId = $request->input('dish_id')) {
			$dish = Dish::findById($dishId);
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

	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id)
	{
		$user = User::findById($id);

		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return mixed
	 */
	public function update(Request $request, $id)
	{
		$validationRule = User::getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$user = User::findById($id);

		$user->update(
			[
				'first_name' => $request->input('first_name'),
				'last_name'  => $request->input('last_name'),
				'phone'      => $request->input('phone'),
			]
		);

		return response()->success($user, 15001);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getSubscriptions(Request $request)
	{
		$user = $this->getUser($request);

		$subscriptionKitchens = $user->getSubscriptions();

		return response()->success($subscriptionKitchens);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getForkedDishes(Request $request)
	{
		$user = $this->getUser($request);

		$forkedDishes = $user->getForkedDishes();

		return response()->success($forkedDishes);
	}
}
