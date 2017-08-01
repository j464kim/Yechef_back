<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Models\DishRating;
use App\Models\OrderItem;
use App\Yechef\Helper;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class DishRatingController extends Controller
{
	protected $dishRating, $dish, $orderItem;

	public function __construct(Application $app, DishRating $dishRating, Dish $dish, OrderItem $orderItem)
	{
		parent::__construct($app);

		$this->dishRating = $dishRating;
		$this->dish = $dish;
		$this->orderItem = $orderItem;
	}

	public function getAvg(Request $request, $dishId)
	{
		$dishRatingsAvg = $this->dish->findById($dishId)->getAvgRatingAttribute();
		return response()->success($dishRatingsAvg);
	}

	public function index(Request $request, $dishId)
	{
		$dishRatings = $this->dish->findById($dishId)->ratings;
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
		$dishRating = $this->dishRating->findById($ratingId);
		return response()->success($dishRating);
	}

	public function store(Request $request, $dishId)
	{
		$validationRule = $this->dishRating->getValidationRule();
		$this->validateInput($request, $validationRule);

		$dish = $this->dish->findById($dishId);
		$orderItem = $this->orderItem->findById($request->orderItemId);
		$user = $this->getUser($request);
		$this->isStoreAllowed($orderItem, $user);

		$rating = $dish->rating([
			'taste_rating'    => $request->input('taste_rating'),
			'visual_rating'   => $request->input('visual_rating'),
			'quantity_rating' => $request->input('quantity_rating'),
			'comment'         => $request->input('comment'),
			'order_item_id'   => $request->orderItemId
		], $user);

		return response()->success($rating, 11004);
	}

	public function update(Request $request, $dishId, $ratingId)
	{
		$validationRule = $this->dishRating->getValidationRule($ratingId);
		$this->validateInput($request, $validationRule);
		$user = $this->getUser($request);
		$dishRating = $this->dishRating->findById($ratingId);

		//check user access
		if ($dishRating->user_id != $user->id) {
			throw new YechefException(15503);
		}

		$rating = $this->dish->updateRating($ratingId, [
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
		$rating = $this->dish->deleteRating($ratingId);
		return response()->success($rating, 11006);
	}

	private function isStoreAllowed($orderItem, $user)
	{
		$order = $orderItem->order;

		//Check user access
		if ($user->id != $order->user_id) {
			throw new YechefException(11503);
		}
		//Expiration check
		if (Carbon::now()->diffInHours($order->updated_at) > 24) {
			throw new YechefException(11504);
		}
		//Check if already rated
		if ($orderItem->dish_rating_id != null) {
			throw new YechefException(11505);
		}
	}

}