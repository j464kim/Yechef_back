<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Yechef\Helper;
use App\Models\Kitchen;
use Illuminate\Support\Facades\Validator;

class KitchenController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$kitchens = Kitchen::with('media')->get();
		// apply pagination
		$result = Helper::paginate($request, $kitchens);
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validateInput($request);

		$kitchen = Kitchen::create([
			'name'        => snake_case($request->input('name')),
			'email'       => $request->input('email'),
			'phone'       => $request->input('phone'),
			'address'     => $request->input('address'),
			'description' => $request->input('description')
		]);

		return response()->success($kitchen);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$kitchen = Kitchen::findKitchen($id, true);
		return response()->success($kitchen);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */

	public function update(Request $request, $id)
	{
		$this->validateInput($request);

		$kitchen = Kitchen::findKitchen($id, true);
		$kitchen->update(
			[
				'name'        => snake_case($request->input('name')),
				'email'       => $request->input('email'),
				'phone'       => $request->input('phone'),
				'address'     => $request->input('address'),
				'description' => $request->input('description')
			]
		);

		return response()->success($kitchen);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Log::info('DESTROY');
		$kitchen = Kitchen::findKitchen($id);
		$kitchen->delete();

		return response()->success(['kitchen is successfully deleted!']);
	}

	private function validateInput(Request $request)
	{
		$validationRule = Kitchen::getValidationRule();
		$validator = Validator::make($request->all(), $validationRule);

		if ($validator->fails()) {
			return response()->fail($validator->messages());
		}
	}
}
