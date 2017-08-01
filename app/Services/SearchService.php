<?php

namespace App\Services;


use App\Models\Dish;
use GeometryLibrary\SphericalUtil;
use Illuminate\Http\Request;

class SearchService
{
	protected $dish;

	public function __construct(Dish $dish)
	{
		$this->dish = $dish;
	}

	public function filter(Request $request, $dishes)
	{
		if ($request->gluten_free === '1') {
			$dishes = $dishes->where('gluten_free', '1');
		}
		if ($request->vegan === '1') {
			$dishes = $dishes->where('vegan', '1');
		}
		if ($request->vegetarian === '1') {
			$dishes = $dishes->where('vegetarian', '1');
		}

		$dishes = $dishes->get()->load('medias')->load([
			'kitchen' => function ($query) use ($request) {
				$query->where('lat', '>', $request->sw_lat)->where('lng', '>', $request->sw_lng)
					->where('lat', '<', $request->ne_lat)->where('lng', '<', $request->ne_lng);
			}
		])->where('kitchen', '!=', null);

		if ($request->input('nationality') !== 'all') {
			$dishes = $dishes->where('nationality', '=', $request->input('nationality'));
		}
		if ($request->input('min_price')) {
			$dishes = $dishes->where('price', '>', $request->input('min_price'));
		}
		if ($request->input('max_price')) {
			$dishes = $dishes->where('price', '<', $request->input('max_price'));
		}

		$userLocationPermitted = $request->userLat && $request->userLng;
		return $dishes->filter(function ($item) use ($request, $userLocationPermitted) {
			$item->addRatingAttributes();
			$dishLat = $item->kitchen->lat;
			$dishLng = $item->kitchen->lng;
			$from = ['lat' => $dishLat, 'lng' => $dishLng];
			if ($userLocationPermitted) {
				$to = ['lat' => $request->userLat, 'lng' => $request->userLng];
			} else {
				$to = ['lat' => $dishLat, 'lng' => $dishLng];
			}
			$item->distance = SphericalUtil::computeDistanceBetween($from, $to);
			if ($request->distance && $request->distance != 0) {
				return ($request->ne_lat >= $dishLat) && ($request->ne_lng >= $dishLng) && ($request->sw_lat <= $dishLat) && ($request->sw_lng <= $dishLng) && ($request->distance >= $item->distance);
			} else {
				return ($request->ne_lat >= $dishLat) && ($request->ne_lng >= $dishLng) && ($request->sw_lat <= $dishLat) && ($request->sw_lng <= $dishLng);
			}
		});
	}


	public function sortBySearch(Request $request, $results)
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