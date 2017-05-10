<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Yechef\Helper;
use Illuminate\Http\Request;

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
		$dish = Dish::with('media')->findOrFail($id);
		return response()->success($dish);
	}

	public function store(Request $request)
	{
		//TODO: No need to require slug input from the user.
		$this->validate($request, [
			'name'        => 'bail|required',
			'description' => 'bail|required',
		]);

		$dish = Dish::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name'        => 'bail|required',
			'description' => 'bail|required',
		]);

		$dish = Dish::findOrFail($id)->update([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'description' => $request->input('description'),
		]);
		return response()->success($dish);
	}

	public function destroy(Request $request, $id)
	{
		//TODO: Need to delete other relationships to prevent foreign key constraint issues
		$dish = Dish::findOrFail($id);
		$dish->delete();
		return response()->success($dish);
	}
}
