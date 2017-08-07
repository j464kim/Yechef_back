<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Models\Dish;
use App\Services\SearchService;
use App\Yechef\Helper;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;


class DishController extends Controller
{

	protected $dish, $searchService;

	public function __construct(Application $app, Dish $dish, SearchService $searchService)
	{
		parent::__construct($app);

		$this->dish = $dish;
		$this->searchService = $searchService;
	}

	public function index(Request $request)
	{
		$dishes = $this->dish->with('medias')->get();
		//TODO: add algorithm to get featured dishes (business model)
		$featuredDishes = $dishes;
		$featuredDishes->filter(function (Dish $item) {
			$item->addRatingAttributes();
		});
		// apply pagination
		$result = Helper::paginate($request, $featuredDishes, 6);
		return response()->success($result);
	}

	public function show(Request $request, $id)
	{
		$dish = $this->dish->findById($id, true);

		return response()->success($dish);
	}

	public function store(Request $request)
	{
		$request->user()->isVerifiedKitchenOwner($request->input('kitchen_id'));

		//TODO: No need to require slug input from the user.
		$validationRule = $this->dish->getValidationRule();
		$this->validateInput($request, $validationRule);

		$dish = $this->dish->create([
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

		$validationRule = $this->dish->getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$dish = $this->dish->findById($id);
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
		$dish = $this->dish->findById($id);
		$request->user()->isVerifiedKitchenOwner($dish->kitchen_id);
		$dish->delete();

		event(new ReactionableDeleted($dish));

		return response()->success($dish, 11003);
	}

	public function search(Request $request)
	{
		$results = $this->dish->search($request->q);
		$filtered = $this->searchService->filter($request, $results);
		$filtered = $this->searchService->sortBySearch($request, $filtered);
		$filtered = Helper::paginate($request, $filtered, 18);
		return response()->success($filtered);
	}
}