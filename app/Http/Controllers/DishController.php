<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Yechef\Helper;
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
		$request->user()->isVerifiedKitchenOwner($request->input('kitchen_id'));

		//TODO: No need to require slug input from the user.
		$validationRule = Dish::getValidationRule();
		$this->validateInput($request, $validationRule);

		$dish = Dish::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
			'nationality' => $request->input('nationality'),
			'gluten_free' => $request->input('gluten_free'),
			'vegetarian'  => $request->input('vegetarian'),
			'vegan'       => $request->input('vegan'),
			//TODO: ingredient
		]);
		$dish->save();
		return response()->success($dish, 11001);
	}

	public function update(Request $request, $id)
	{
		$request->user()->isVerifiedKitchenOwner($request->input('kitchen_id'));

		$validationRule = Dish::getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$dish = Dish::findDish($id);
		$dish->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
			'nationality' => $request->input('nationality'),
			'gluten_free' => $request->input('gluten_free'),
			'vegetarian'  => $request->input('vegetarian'),
			'vegan'       => $request->input('vegan'),
			//TODO: ingredient
		]);
		$dish->save();
		return response()->success($dish, 11002);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		//TODO: Also need to delete associated ratings
		$dish = Dish::findDish($id);
		$request->user()->isVerifiedKitchenOwner($dish->kitchen_id);
		$dish->delete();

		event(new ReactionableDeleted($dish));

		return response()->success($dish, 11003);
	}

	public function search(Request $request)
	{
		if (!$request->city) {
			throw new YechefException();
		}
		$results = Dish::search($request->q);
		if ($request->gluten_free === '1') {
			$results = $results->where('gluten_free', '1');
		}
		if ($request->vegan === '1') {
			$results = $results->where('vegan', '1');
		}
		if ($request->vegetarian === '1') {
			$results = $results->where('vegetarian', '1');
		}

		$results = $results->get()->load('medias')->load(['kitchen' => function ($query)  use ($request){
			$query->where('address', 'like', "%$request->city%");
		}])->where('kitchen', '!=', null);

		if ($request->input('nationality') !== 'all') {
			$results = $results->where('nationality', '=', $request->input('nationality'));
		}
		if ($request->input('min_price')) {
			$results = $results->where('price', '>', $request->input('min_price'));
		}
		if ($request->input('max_price')) {
			$results = $results->where('price', '<', $request->input('max_price'));
		}
		$results = Helper::paginate($request, $results, 18);
		return response()->success($results);
	}
}