<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\DishRating;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class DishRatingController extends Controller
{
	private $validator;

	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	public function getAvg(Request $request, $dishId)
	{
		$dishRatingsAvg = Dish::findDish($dishId)->getAvgRatingAttribute();
		return response()->success($dishRatingsAvg);
	}

	public function index(Request $request, $dishId)
	{
		$dishRatings = Dish::findDish($dishId)->ratings;
		// apply pagination
		$result = Helper::paginate($request, $dishRatings, 10);
		return response()->success($result);
	}

	public function show(Request $request, $dishId, $ratingId)
	{
		$dishRating = DishRating::findDishRating($ratingId);
		return response()->success($dishRating);
	}

	public function store(Request $request, $dishId)
	{
		//TODO: No need to require slug input from the user.
		$this->validateRequestInputs($request);
		$dish = Dish::findDish($dishId);
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
		$this->validateRequestInputs($request);
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

	private function validateRequestInputs($request)
	{
		$validator = $this->validator->make($request->all(), DishRating::getValidation());
		If ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(11502, $message);
		}
	}
}