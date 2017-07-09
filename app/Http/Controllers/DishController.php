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
		$dish = Dish::findById($id, true);

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

		$dish = Dish::findById($id);
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
		$dish = Dish::findById($id);
		$request->user()->isVerifiedKitchenOwner($dish->kitchen_id);
		$dish->delete();

		event(new ReactionableDeleted($dish));

		return response()->success($dish, 11003);
	}

	public function search(Request $request)
	{
		if (!$request->city) {
			throw new YechefException(11502);
		}
		$pieces = explode(",", $request->city);
		$request->city = implode(',', [$pieces[0], $pieces[1]]);
		$results = Dish::search($request->q);
		$filtered = Dish::filter($request, $results);
		$filtered = $this->sortBySearch($request, $filtered);
		$filtered = Helper::paginate($request, $filtered, 18);
		return response()->success($filtered);
	}

	private function sortBySearch(Request $request, $results)
	{
		$sortBy = $request->input('sortBy') ?: null;

		if (!$sortBy) {
			return $results;
		}

		foreach ($results as $result) {
			$result['taste_rating'] = $result->avgRating['taste_rating'];
			$result['visual_rating'] = $result->avgRating['visual_rating'];
			$result['quantity_rating'] = $result->avgRating['quantity_rating'];
		}

		switch ($sortBy) {
			case 'price_asc':
				$results = $results->sortBy('price');
				break;

			case 'price_dsc':
				$results = $results->sortByDesc('price');
				break;

			case 'newest':
				$results = $results->sortBy('created_at');
				break;

			case 'taste':
				$results = $results->sortByDesc('taste_rating');
				break;

			case 'visual':
				$results = $results->sortByDesc('visual_rating');
				break;

			case 'quantity':
				$results = $results->sortByDesc('quantity_rating');
				break;
			default:
				break;
		}

		// reset index of sorted results (necessary since front-end resorts the result based on php collection index)
		$results = $results->values();

		return $results;

	}
}