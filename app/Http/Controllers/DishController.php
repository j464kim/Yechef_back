<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Yechef\Helper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DishController extends Controller
{
	public function index(Request $request)
	{
		$dish = Dish::with('media')->get();
		// apply pagination
		$result = Helper::paginate($request, $dish);
		return response()->success($result);
	}

	public function show(Request $request, $id)
	{
		$dish = $this->findDish($id, true);
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
		return response()->success($dish);
	}

	public function update(Request $request, $id)
	{
		$this->validateRequestInputs($request, $id);
		$dish = $this->findDish($id)->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		$dish = $this->findDish($id);
		$dish->delete();
		return response()->success($dish);
	}

	private function validateRequestInputs($request, $id = null)
	{
		//TODO: Return the failure response as json
		$validator = Validator::make($request->all(), Dish::getvalidation($id));
		If ($validator->fails()) {
			Return response()->fail($validator->errors()->first());
		}
	}

	private function findDish($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return Dish::with('media')->findOrFail($id);

			} else {
				return Dish::findOrFail($id);
			}
		} catch (ModelNotFoundException $ex) {
			//TODO: Despite of an exception case, it returns 200 status code no matter.
			//Abort could not be used since we want to return json response..
			//(some open source community admitted that it is flaky)
			Log::warning('Could not find the dish with id: ' . $id);
//			abort(422, 'Could not find the dish with id: ' . $id);

			//TODO: Return the failure response as json
			return response()->json($ex->getMessage(), 422);
		}
	}
}