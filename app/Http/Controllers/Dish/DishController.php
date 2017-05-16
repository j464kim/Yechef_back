<?php

namespace App\Http\Controllers\Dish;

use App\Events\DishDeleted;
use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
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
		$dish = Dish::with('media')->get();
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
		]);
		return response()->success($dish, 11001);
	}

	public function update(Request $request, $id)
	{
		$this->validateRequestInputs($request, $id);
		$dish = Dish::findDish($id);
		$dish->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish, 11002);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		//TODO: Also need to delete associated ratings
		$dish = Dish::findDish($id);
		$dish->delete();
		event(new DishDeleted($dish));
		return response()->success($dish, 11003);
	}

	private function validateRequestInputs($request, $id = null)
	{
		$validator = $this->validator->make($request->all(), Dish::getValidation($id));
		If ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(11501, $message);
		}
	}
}