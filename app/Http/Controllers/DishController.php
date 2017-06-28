<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Yechef\Helper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class DishController extends Controller
{
	private $validator;

	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

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
		$this->validateRequestInputs($request);
		$dish = Dish::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
			//TODO: ingredient
		]);
		$dish->save();
		return response()->success($dish, 11001);
	}

	public function update(Request $request, $id)
	{
		$this->validateRequestInputs($request);
		$dish = Dish::findDish($id);
		$dish->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
			'price'       => $request->input('price'),
			'kitchen_id'  => $request->input('kitchen_id'),
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
		$dish->delete();

		event(new ReactionableDeleted($dish));

		return response()->success($dish, 11003);
	}

	public function search(Request $request)
	{
		$results = Dish::search($request->q)->where('gluten_free', $request->gluten_free)->where('vegan',
			$request->vegan)->where('vegetarian', $request->vegetarian)->get()->load('medias');
		if ($request->input('min_price')) {
			$results = $results->where('price', '>', $request->input('min_price'));
		}
		if ($request->input('max_price')) {
			$results = $results->where('price', '<', $request->input('max_price'));
		}
		if ($request->input('nationality') !== 'all') {
			$results = $results->where('nationality', '=', $request->input('nationality'));
		}
		$results = Helper::paginate($request, $results, 18);
		return response()->success($results);
	}

	private function validateRequestInputs($request)
	{
		$validator = $this->validator->make($request->all(), Dish::getValidation());
		If ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(11501, $message);
		}
	}
}