<?php

namespace App\Http\Controllers\Dish;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Rating\DishRating;
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

	public function index(Request $request)
	{
		$dishRatings = Dish::findDish($request->input('dishId'))->ratings;
		// apply pagination
		$result = Helper::paginate($request, $dishRatings, 10);
		return response()->success($result);
	}

	public function show(Request $request, $id)
	{
		$dishRating = DishRating::findDishRating($id);
		return response()->success($dishRating);
	}

	public function store(Request $request)
	{
		//TODO: No need to require slug input from the user.
		$this->validateRequestInputs($request);
		$dish = Dish::findDish($request->input('dishId'));
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

	public function update(Request $request, $id)
	{
		//TODO: Check if the user has the permission to do so
		$this->validateRequestInputs($request, $id);
		$rating = Dish::updateRating($id, [
			'taste_rating'    => $request->input('taste_rating'),
			'visual_rating'   => $request->input('visual_rating'),
			'quantity_rating' => $request->input('quantity_rating'),
			'comment'         => $request->input('comment'),
		]);
		return response()->success($rating, 11005);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Check if the user has the permission to do so
		$rating = Dish::deleteRating($id);
		return response()->success($rating, 11006);
	}

	private function validateRequestInputs($request, $id = null)
	{
		$validator = $this->validator->make($request->all(), DishRating::getValidation($id));
		If ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(11502, $message);
		}
	}
}