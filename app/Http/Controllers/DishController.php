<?php

namespace App\Http\Controllers;

use App\Events\DishDeleted;
use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Yechef\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DishController extends Controller
{
	public function index(Request $request)
	{
		$dish = Dish::with('media')->get();
		// apply pagination
		$result = Helper::paginate($request, $dish);
		return response()->success($result);
	}

	public function show(Request $request, $id)
	{
		$dish = Dish::findDish($id, true);
		return response()->success($dish);
	}

	public function store(Request $request)
	{
		//TODO: No need to require slug input from the user.
		$this->validateRequestInputs($request);
		$dish = Dish::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish);
	}

	public function update(Request $request, $id)
	{
		$this->validateRequestInputs($request, $id);
		$dish = Dish::findDish($id)->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		$dish = Dish::findDish($id);
		$dish->delete();
		event(new DishDeleted($dish));
		return response()->success($dish);
	}

	private function validateRequestInputs($request, $id = null)
	{
		//TODO: Return the failure response as json
		$validator = Validator::make($request->all(), Dish::getValidation($id));
		If ($validator->fails()) {
			throw new YechefException(4);
		}
	}
}