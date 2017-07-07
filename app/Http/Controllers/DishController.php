<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Yechef\Helper;
use GeometryLibrary\SphericalUtil;
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

		if ($request->gluten_free === '1') {
			$results = $results->where('gluten_free', '1');
		}
		if ($request->vegan === '1') {
			$results = $results->where('vegan', '1');
		}
		if ($request->vegetarian === '1') {
			$results = $results->where('vegetarian', '1');
		}

		$results = $results->get()->load('medias')->load([
			'kitchen' => function ($query) use ($request) {
				$query->where('address', 'like', "%$request->city%");
			}
		])->where('kitchen', '!=', null);

		if ($request->input('nationality') !== 'all') {
			$results = $results->where('nationality', '=', $request->input('nationality'));
		}
		if ($request->input('min_price')) {
			$results = $results->where('price', '>', $request->input('min_price'));
		}
		if ($request->input('max_price')) {
			$results = $results->where('price', '<', $request->input('max_price'));
		}

		$filtered = $results->filter(function ($item) use ($request) {
			$geoCodedAddress = \GoogleMaps::load('geocoding')->setParamByKey('address', $item->kitchen->address)->get();
			$geoCodedAddress = json_decode($geoCodedAddress);
			$lat = $geoCodedAddress->results[0]->geometry->location->lat;
			$lng = $geoCodedAddress->results[0]->geometry->location->lng;
			$item->lat = $lat;
			$item->lng = $lng;
			$from = ['lat' => $lat, 'lng' => $lng];
			$to = ['lat' => $request->userLat, 'lng' => $request->userLng];
			$item->distance = SphericalUtil::computeDistanceBetween($from, $to);
			if ($request->distance && $request->distance != 0) {
				return ($request->NE_lat >= $lat) && ($request->NE_lng >= $lng) && ($request->SW_lat <= $lat) && ($request->SW_lng <= $lng) && ($request->distance >= $item->distance);
			} else {
				return ($request->NE_lat >= $lat) && ($request->NE_lng >= $lng) && ($request->SW_lat <= $lat) && ($request->SW_lng <= $lng);
			}
		});

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