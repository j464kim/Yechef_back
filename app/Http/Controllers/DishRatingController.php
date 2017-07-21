<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishRating;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Http\Request;

class DishRatingController extends Controller
{

	public function getAvg(Request $request, $dishId)
	{
		$dishRatingsAvg = Dish::findById($dishId)->getAvgRatingAttribute();
		return response()->success($dishRatingsAvg);
	}

	public function index(Request $request, $dishId)
	{
		$dishRatings = Dish::findById($dishId)->ratings;
		$dishRatings->load([
			'user' => function ($query) {
				$query->with('medias');
			}
		]);
		// apply pagination
		$result = Helper::paginate($request, $dishRatings, 10);
		return response()->success($result);
	}

	public function show(Request $request, $dishId, $ratingId)
	{
		$dishRating = DishRating::findById($ratingId);
		return response()->success($dishRating);
	}

	public function store(Request $request, $dishId)
	{
		//TODO: No need to require slug input from the user.
		$validationRule = DishRating::getValidationRule();
		$this->validateInput($request, $validationRule);

		$dish = Dish::findById($dishId);
		//TODO: Replace with the real user
		$user = User::first();
		$rating = $dish->rating([
			'taste_rating'    => $request->input('taste_rating'),
			'visual_rating'   => $request->input('visual_rating'),
			'quantity_rating' => $request->input('quantity_rating'),
			'comment'         => $request->input('comment'),
		], $user);
		return response()->success($rating, 11004);
	}

	public function update(Request $request, $dishId, $ratingId)
	{
		//TODO: Check if the user has the permission to do so
		$validationRule = DishRating::getValidationRule($ratingId);
		$this->validateInput($request, $validationRule);

		$rating = Dish::updateRating($ratingId, [
			'taste_rating'    => $request->input('taste_rating'),
			'visual_rating'   => $request->input('visual_rating'),
			'quantity_rating' => $request->input('quantity_rating'),
			'comment'         => $request->input('comment'),
		]);
		return response()->success($rating, 11005);
	}

	public function destroy(Request $request, $dishId, $ratingId)
	{
		//TODO: Check if the user has the permission to do so
		$rating = Dish::deleteRating($ratingId);
		return response()->success($rating, 11006);
	}

}