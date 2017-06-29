<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Yechef\Helper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class DishController extends Controller
{

	public function index(Request $request)
	{
		$dish = Dish::with('medias')->get();
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
		$validationRule = Dish::getValidationRule();
		$this->validateInput($request, $validationRule);

		$dish = Dish::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
			//TODO: ingredient
		]);
		return response()->success($dish, 11001);
	}

	public function update(Request $request, $id)
	{
		$validationRule = Dish::getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$dish = Dish::findDish($id);
		$dish->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
			//TODO: ingredient
		]);
		return response()->success($dish, 11002);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		//TODO: Also need to delete associated ratings
		$dish = Dish::findDish($id);
		$dish->delete();

		event(new ReactionableDeleted($dish));

		return response()->success($dish, 11003);
	}

}